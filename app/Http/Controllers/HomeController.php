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

        // Format tanggal hari ini
        $today = Carbon::now()->format('d-m-Y');

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


        // Ambil tanggal hijriyah juga (bonus)
        $tanggalHijriyah = $response['data']['date']['hijri']['day'] . ' ' .
                           $response['data']['date']['hijri']['month']['en'] . ' ' .
                           $response['data']['date']['hijri']['year'] . ' H';

        return view('frontend.home', compact('jadwal', 'tanggalHijriyah'));
    }

    public function hijriCalendar()
    {
        $latitude = -6.450593;
        $longitude = 107.038322;
        $method = 11;

        $now = Carbon::now();
        Carbon::setLocale('id');

        $year = (int) $now->year;
        $month = (int) $now->month;
        $gregLabel = $now->translatedFormat('F Y');

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
        $hijriMonthLabel = $gregLabel;

        $cacheKey = "hijri_calendar_{$year}_{$month}";
        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            $hijriDays = $cached['days'] ?? [];
            $hijriMonthLabel = $cached['label'] ?? $gregLabel;
        } else {
            try {
                $url = "https://api.aladhan.com/v1/gToHCalendar/{$month}/{$year}";
                $api = Http::timeout(12)
                    ->acceptJson()
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get($url);

                // Fallback: try with location params if the plain call fails
                if (!$api->ok()) {
                    $api = Http::timeout(12)
                        ->acceptJson()
                        ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                        ->get($url, [
                            'latitude'  => $latitude,
                            'longitude' => $longitude,
                            'method'    => $method,
                            'timezone'  => 'Asia/Jakarta',
                        ]);
                }

                $rows = $api->ok() ? $api->json('data') : null;

                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        $gDay = isset($row['date']['gregorian']['day'])
                            ? (int) $row['date']['gregorian']['day']
                            : null;
                        $hDay = $row['date']['hijri']['day'] ?? null;

                        if ($gDay && $hDay) {
                            $hijriDays[$gDay] = $hDay;
                        }

                        if ($gDay === (int) $now->format('j')) {
                            $hMonthEn = $row['date']['hijri']['month']['en'] ?? null;
                            $hYear = $row['date']['hijri']['year'] ?? null;
                            if ($hMonthEn && $hYear) {
                                $hMonth = $hijriMonthMap[$hMonthEn] ?? $hMonthEn;
                                $hijriMonthLabel = "{$hMonth} {$hYear} H / {$gregLabel}";
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // keep fallback labels
            }

            // If calendar API failed, fallback to per-day conversion
            if (empty($hijriDays)) {
                $daysInMonth = $now->daysInMonth;
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    try {
                        $date = Carbon::create($year, $month, $day)->format('d-m-Y');
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
                        $hDay = $data['hijri']['day'] ?? null;
                        if ($hDay) {
                            $hijriDays[$day] = $hDay;
                        }

                        if ($day === (int) $now->format('j')) {
                            $hMonthEn = $data['hijri']['month']['en'] ?? null;
                            $hYear = $data['hijri']['year'] ?? null;
                            if ($hMonthEn && $hYear) {
                                $hMonth = $hijriMonthMap[$hMonthEn] ?? $hMonthEn;
                                $hijriMonthLabel = "{$hMonth} {$hYear} H / {$gregLabel}";
                            }
                        }
                    } catch (\Throwable $e) {
                        // keep going
                    }
                }
            }

            Cache::put($cacheKey, [
                'days' => $hijriDays,
                'label' => $hijriMonthLabel,
            ], 86400);
        }

        return view('frontend.hijri-calendar', compact('hijriDays', 'hijriMonthLabel'));
    }
}
