@extends('layouts.app')

@section('title', 'Agenda Jumat')

@section('content')
<div class="container mt-2 mb-4">
    <div class="card shadow-sm border-0 agenda-card">
        <div class="agenda-header text-center">
            <div class="agenda-header-title text-uppercase fw-semibold">Informasi Sholat Jum'at</div>
            <div class="agenda-header-subtitle fw-semibold">Masjid Jami' ABI MUSA AL ASY'ARI - Bukit Cendana</div>
        </div>
        <div class="card-body p-4">

            @php
                $dayLabel = $targetDate ? $targetDate->translatedFormat('l, d F Y') : '-';
                $dayLabel = str_replace('Jumat', "Jum'at", $dayLabel);
                $hijriDisplay = $hijriLabel ?? '-';
                $dhuhrDisplay = $dhuhrTime ? str_replace(':', '.', $dhuhrTime) : '--.--';
            @endphp

            <div class="agenda-info agenda-info-plain">
                <div class="agenda-item">
                    <span class="agenda-emoji" aria-hidden="true">&#128197;</span>
                    <div>
                        <div class="agenda-label">Hari/Tgl</div>
                        <div class="agenda-value"><strong>{{ $dayLabel }}</strong> | {{ $hijriDisplay }}</div>
                    </div>
                </div>
                <div class="agenda-item">
                    <span class="agenda-emoji" aria-hidden="true">&#128115;&#8205;&#9794;&#65039;</span>
                    <div>
                        <div class="agenda-label">Khotib</div>
                        <div class="agenda-value"><strong>{{ $schedule->khotib_name ?? '-' }}</strong></div>
                    </div>
                </div>
                <div class="agenda-item">
                    <span class="agenda-emoji" aria-hidden="true">
                    <svg viewBox="0 0 64 64" width="22" height="22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="32" cy="22" r="10" fill="#5c6b7a"/>
                        <path d="M16 54c2-10 10-18 16-18s14 8 16 18H16Z" fill="#5c6b7a"/>
                    </svg>
                    </span>
                    <div>
                        <div class="agenda-label">Bilal</div>
                        <div class="agenda-value"><strong>{{ $schedule->bilal ?? 'Bp. Adi' }}</strong></div>
                    </div>
                </div>
                <div class="agenda-item agenda-item-highlight">
                    <span class="agenda-emoji" aria-hidden="true">&#128227;</span>
                    <div>
                        <div class="agenda-label">Waktu Zuhur</div>
                        <div class="agenda-value agenda-badge">{{ $dhuhrDisplay }} WIB</div>
                    </div>
                </div>
            </div>

            <div class="agenda-infaq">
                <p class="mb-2">INFAQ dan Shodaqoh dapat disalurkan melalui Bank Syariah Indonesia</p>
                <div class="fw-semibold">No Rek : 7126720909</div>
                <div class="fw-semibold mb-3">a/n MASJID ABU MUSA AL ASY'ARI</div>
                <a class="agenda-maps" href="https://maps.app.goo.gl/QBiFWNgz5YQmHeb47" target="_blank" rel="noopener">Lihat Lokasi Masjid</a>
            </div>

            <div class="agenda-note">Note : Pembaruan jadwal otomatis ter-update setiap Kamis, Pukul : 20.00 WIB</div>
        </div>
    </div>
</div>
@endsection
