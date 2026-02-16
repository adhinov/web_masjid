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
}
