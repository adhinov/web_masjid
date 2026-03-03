<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // Koordinat Masjid Abi Musa Al-Asy'ari
        $latitude = -6.450593;
        $longitude = 107.038322;

        // Method 11 = Kemenag Indonesia
        $method = 11;
        $hijriOffsetDays = -1;

        // Format tanggal hari ini
        $todayCarbon = Carbon::now();
        $today = $todayCarbon->format('d-m-Y');

        // Cache per hari (86400 detik = 1 hari)
        try {
            $response = Cache::remember('jadwal_sholat_' . $today, 86400, function () use ($latitude, $longitude, $method, $today) {
                $url = "https://api.aladhan.com/v1/timings/{$today}";

                // Primary: Laravel HTTP client
                $api = Http::timeout(10)
                    ->acceptJson()
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get($url, [
                        'latitude'  => $latitude,
                        'longitude' => $longitude,
                        'method'    => $method,
                        'timezone'  => 'Asia/Jakarta'
                    ]);

                if ($api->ok()) {
                    return $api->json();
                }

                // Fallback: file_get_contents (in case HTTP client fails on server)
                $query = http_build_query([
                    'latitude'  => $latitude,
                    'longitude' => $longitude,
                    'method'    => $method,
                    'timezone'  => 'Asia/Jakarta'
                ]);
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 10,
                        'header'  => "User-Agent: Mozilla/5.0\r\nAccept: application/json\r\n"
                    ]
                ]);
                $raw = @file_get_contents($url . '?' . $query, false, $context);

                return $raw ? json_decode($raw, true) : null;
            });
        } catch (\Throwable $e) {
            $response = null;
        }

        if (!is_array($response) || !isset($response['data']['timings'])) {
            $jadwal = [
                'Imsak' => '--:--',
                'Subuh' => '--:--',
                'Dzuhur' => '--:--',
                'Ashar' => '--:--',
                'Maghrib' => '--:--',
                'Isya' => '--:--',
            ];

            $tanggalHijriyah = 'Tanggal Hijriyah tidak tersedia';

            return view('frontend.home', compact('jadwal', 'tanggalHijriyah'));
        }

        // Filter hanya 6 waktu utama
        $jadwal = [
            'Imsak' => $response['data']['timings']['Imsak'],
            'Subuh' => $response['data']['timings']['Fajr'],
            'Dzuhur' => $response['data']['timings']['Dhuhr'],
            'Ashar' => $response['data']['timings']['Asr'],
            'Maghrib' => $response['data']['timings']['Maghrib'],
            'Isya' => $response['data']['timings']['Isha'],
        ];


        // Ambil tanggal hijriyah (mengacu offset)
        $tanggalHijriyah = $response['data']['date']['hijri']['day'] . ' ' .
                           $response['data']['date']['hijri']['month']['en'] . ' ' .
                           $response['data']['date']['hijri']['year'] . ' H';

        try {
            $hijriDate = $todayCarbon->copy()->addDays($hijriOffsetDays)->format('d-m-Y');
            $hijriApi = Http::timeout(8)
                ->acceptJson()
                ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                ->get('https://api.aladhan.com/v1/gToH', [
                    'date' => $hijriDate,
                ]);

            if ($hijriApi->ok() && isset($hijriApi['data']['hijri'])) {
                $h = $hijriApi['data']['hijri'];
                $tanggalHijriyah = $h['day'] . ' ' . $h['month']['en'] . ' ' . $h['year'] . ' H';
            }
        } catch (\Throwable $e) {
            // keep fallback hijri date from timings
        }

        return view('frontend.home', compact('jadwal', 'tanggalHijriyah'));
    }

    public function hijriCalendar(Request $request)
    {
        $latitude = -6.450593;
        $longitude = 107.038322;
        $method = 11;
        $offsetDays = -1;

        $now = Carbon::now();
        Carbon::setLocale('id');

        $requestedYear = (int) $request->query('year', (int) $now->year);
        $requestedMonth = (int) $request->query('month', (int) $now->month);

        $year = 2026;
        $month = $requestedMonth;

        if ($requestedYear !== 2026) {
            $year = 2026;
        }

        if ($month < 1) {
            $month = 1;
        } elseif ($month > 12) {
            $month = 12;
        }

        $displayDate = Carbon::create($year, $month, 1);
        $gregLabel = $displayDate->translatedFormat('F Y');

        $hijriMonthMap = [
            'Muharram' => 'Muharram',
            'Safar' => 'Safar',
            "Rabi' Al-Awwal" => 'Rabiul Awal',
            "Rabi' Al-Thani" => 'Rabiul Akhir',
            'Jumada Al-Awwal' => 'Jumadil Awal',
            'Jumada Al-Thani' => 'Jumadil Akhir',
            'Rajab' => 'Rajab',
            "Sha'ban" => 'Syaban',
            'Ramadan' => 'Ramadan',
            'Shawwal' => 'Syawal',
            "Dhul-Qa'dah" => 'Zulkaidah',
            'Dhul-Hijjah' => 'Zulhijah',
        ];

        $hijriDays = [];
        $hijriMonthLabel = '';
        $hijriRangeLabel = '';
        $hijriMonthByDay = [];
        $firstHijriMeta = null;
        $lastHijriMeta = null;

        $cacheKey = "hijri_calendar_v6_{$year}_{$month}_offset{$offsetDays}";
        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            $hijriDays = $cached['days'] ?? [];
            $hijriMonthLabel = $cached['label'] ?? '';
            $hijriRangeLabel = $cached['range'] ?? '';
            $hijriMonthByDay = $cached['months'] ?? [];
        } else {
            $daysInMonth = $displayDate->daysInMonth;
            for ($day = 1; $day <= $daysInMonth; $day++) {
                try {
                    $date = Carbon::create($year, $month, $day)
                        ->addDays($offsetDays)
                        ->format('d-m-Y');
                    $api = Http::timeout(8)
                        ->acceptJson()
                        ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                        ->get('https://api.aladhan.com/v1/gToH', [
                            'date' => $date,
                        ]);

                    if (!$api->ok()) {
                        continue;
                    }

                    $data = $api->json('data');
                    $hijriMeta = $data['hijri'] ?? null;
                    $hDay = $hijriMeta['day'] ?? null;
                    if ($hDay) {
                        $hijriDays[$day] = $hDay;
                    }
                    if ($hijriMeta) {
                        $hijriMonthByDay[$day] = [
                            'month' => $hijriMeta['month']['en'] ?? null,
                            'year' => $hijriMeta['year'] ?? null,
                        ];
                    }

                    if ($hijriMeta && !$firstHijriMeta) {
                        $firstHijriMeta = $hijriMeta;
                    }

                    if ($hijriMeta) {
                        $lastHijriMeta = $hijriMeta;
                    }

                    if ($day === 15 && $hijriMeta) {
                        $hMonthEn = $hijriMeta['month']['en'] ?? null;
                        $hYear = $hijriMeta['year'] ?? null;
                        if ($hMonthEn && $hYear) {
                            $hMonth = $hijriMonthMap[$hMonthEn] ?? $hMonthEn;
                            $hijriMonthLabel = "{$hMonth} {$hYear}";
                        }
                    }
                } catch (\Throwable $e) {
                    // keep going
                }
            }

            // Build Hijri month range label (e.g., Ramadan 1447 - Shawwal 1447)
            try {
                $firstDate = Carbon::create($year, $month, 1)->addDays($offsetDays)->format('d-m-Y');
                $lastDate = Carbon::create($year, $month, $daysInMonth)->addDays($offsetDays)->format('d-m-Y');

                $firstApi = Http::timeout(8)
                    ->acceptJson()
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get('https://api.aladhan.com/v1/gToH', [
                        'date' => $firstDate,
                    ]);

                $lastApi = Http::timeout(8)
                    ->acceptJson()
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get('https://api.aladhan.com/v1/gToH', [
                        'date' => $lastDate,
                    ]);

                if ($firstApi->ok() && $lastApi->ok()) {
                    $firstHijri = $firstApi->json('data.hijri');
                    $lastHijri = $lastApi->json('data.hijri');

                    $firstMonthEn = $firstHijri['month']['en'] ?? null;
                    $lastMonthEn = $lastHijri['month']['en'] ?? null;
                    $firstYear = $firstHijri['year'] ?? null;
                    $lastYear = $lastHijri['year'] ?? null;

                    if ($firstMonthEn && $lastMonthEn && $firstYear && $lastYear) {
                        $firstMonth = $hijriMonthMap[$firstMonthEn] ?? $firstMonthEn;
                        $lastMonth = $hijriMonthMap[$lastMonthEn] ?? $lastMonthEn;
                        if ($firstMonth === $lastMonth && $firstYear === $lastYear) {
                            $hijriRangeLabel = "{$firstMonth} {$firstYear}";
                        } else {
                            $hijriRangeLabel = "{$firstMonth} {$firstYear} - {$lastMonth} {$lastYear}";
                        }
                    }
                }
            } catch (\Throwable $e) {
                // keep empty if fails
            }

            // Fallback range label from looped metadata if API range failed
            if (empty($hijriRangeLabel)) {
                $firstMonthEn = null;
                $lastMonthEn = null;
                $firstYear = null;
                $lastYear = null;

                ksort($hijriMonthByDay);
                foreach ($hijriMonthByDay as $meta) {
                    if (!empty($meta['month']) && !empty($meta['year'])) {
                        $firstMonthEn = $meta['month'];
                        $firstYear = $meta['year'];
                        break;
                    }
                }

                $reversed = array_reverse($hijriMonthByDay, true);
                foreach ($reversed as $meta) {
                    if (!empty($meta['month']) && !empty($meta['year'])) {
                        $lastMonthEn = $meta['month'];
                        $lastYear = $meta['year'];
                        break;
                    }
                }

                if ($firstMonthEn && $lastMonthEn && $firstYear && $lastYear) {
                    $firstMonth = $hijriMonthMap[$firstMonthEn] ?? $firstMonthEn;
                    $lastMonth = $hijriMonthMap[$lastMonthEn] ?? $lastMonthEn;
                    if ($firstMonth === $lastMonth && $firstYear === $lastYear) {
                        $hijriRangeLabel = "{$firstMonth} {$firstYear}";
                    } else {
                        $hijriRangeLabel = "{$firstMonth} {$firstYear} - {$lastMonth} {$lastYear}";
                    }
                }
            }

            if (empty($hijriMonthLabel) && $firstHijriMeta) {
                $fallbackMonthEn = $firstHijriMeta['month']['en'] ?? null;
                $fallbackYear = $firstHijriMeta['year'] ?? null;
                if ($fallbackMonthEn && $fallbackYear) {
                    $fallbackMonth = $hijriMonthMap[$fallbackMonthEn] ?? $fallbackMonthEn;
                    $hijriMonthLabel = "{$fallbackMonth} {$fallbackYear}";
                }
            }

            // Fill any missing hijri days by simple sequence fallback
            if (!empty($hijriDays)) {
                // Forward fill
                $lastHijri = null;
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    if (isset($hijriDays[$day])) {
                        $lastHijri = (int) $hijriDays[$day];
                        continue;
                    }

                    if ($lastHijri !== null) {
                        $lastHijri++;
                        if ($lastHijri > 30) {
                            $lastHijri = 1;
                        }
                        $hijriDays[$day] = (string) $lastHijri;
                    }
                }

                // Backward fill if the first days are missing
                $firstKnownDay = null;
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    if (isset($hijriDays[$day])) {
                        $firstKnownDay = $day;
                        break;
                    }
                }

                if ($firstKnownDay && $firstKnownDay > 1) {
                    $hijriValue = (int) $hijriDays[$firstKnownDay];
                    for ($day = $firstKnownDay - 1; $day >= 1; $day--) {
                        $hijriValue--;
                        if ($hijriValue < 1) {
                            $hijriValue = 30;
                        }
                        $hijriDays[$day] = (string) $hijriValue;
                    }
                }
            }

            Cache::put($cacheKey, [
                'days' => $hijriDays,
                'label' => $hijriMonthLabel,
                'range' => $hijriRangeLabel,
                'months' => $hijriMonthByDay,
            ], 86400);
        }

        $holidayMap = [];
        $holidayList = [];

        $holidayByYear = [
            2026 => [
                '2026-01-16' => ["Isra Mi'raj 1447 H"],
                '2026-02-19' => ['Awal Ramadan 1447 H'],
                '2026-03-07' => ["Nuzulul Qur'an 17 Ramadan"],
                '2026-03-21' => ['Idul Fitri 1 Syawal 1447 H'],
                '2026-03-22' => ['Idul Fitri (Hari ke-2)'],
                '2026-05-27' => ['Idul Adha 10 Dzulhijjah 1447 H'],
                '2026-06-16' => ['Tahun Baru Islam 1 Muharram 1448 H'],
                '2026-08-25' => ['Maulid Nabi 12 Rabiul Awal 1448 H'],
            ],
        ];

        $holidaySource = $holidayByYear[$year] ?? [];
        foreach ($holidaySource as $date => $names) {
            try {
                $dateCarbon = Carbon::createFromFormat('Y-m-d', $date);
            } catch (\Throwable $e) {
                continue;
            }

            if ((int) $dateCarbon->year !== $year || (int) $dateCarbon->month !== $month) {
                continue;
            }

            $day = (int) $dateCarbon->day;
            $holidayMap[$day] = $names;

            foreach ($names as $name) {
                $holidayList[] = [
                    'date' => $dateCarbon->translatedFormat('d F Y'),
                    'name' => $name,
                ];
            }
        }

        $isCurrentMonth = ($now->year === $year && (int) $now->month === $month);

        $prevMonth = $month - 1;
        $nextMonth = $month + 1;
        $prevLink = null;
        $nextLink = null;

        if ($prevMonth >= 1) {
            $prevLink = route('hijri.calendar', ['year' => $year, 'month' => $prevMonth]);
        }

        if ($nextMonth <= 12) {
            $nextLink = route('hijri.calendar', ['year' => $year, 'month' => $nextMonth]);
        }

        return view('frontend.hijri-calendar', compact(
            'hijriDays',
            'hijriMonthLabel',
            'hijriRangeLabel',
            'holidayMap',
            'holidayList',
            'displayDate',
            'isCurrentMonth',
            'prevLink',
            'nextLink'
        ));
    }
}
