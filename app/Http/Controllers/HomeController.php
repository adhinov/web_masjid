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
        $response = Cache::remember('jadwal_sholat_' . $today, 86400, function () use ($latitude, $longitude, $method, $today) {

            $api = Http::get("https://api.aladhan.com/v1/timings/{$today}", [
                'latitude'  => $latitude,
                'longitude' => $longitude,
                'method'    => $method,
                'timezone'  => 'Asia/Jakarta'
            ]);

            return $api->json();
        });

        // Ambil data waktu sholat
        $timings = $response['data']['timings'];

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
