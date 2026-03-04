@extends('layouts.app')

@section('title', 'Agenda Jumat')

@section('content')
<div class="container my-4">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <div class="text-uppercase fw-semibold text-masjid fs-4">Informasi Sholat Jum'at</div>
                <div class="fw-semibold agenda-subtitle">Masjid Jami' ABI MUSA AL ASY'ARI - Bukit Cendana</div>
            </div>

            @php
                $dayLabel = $targetDate ? $targetDate->translatedFormat('l, d F Y') : '-';
                $dayLabel = str_replace('Jumat', "Jum'at", $dayLabel);
                $hijriDisplay = $hijriLabel ?? '-';
                $dhuhrDisplay = $dhuhrTime ? str_replace(':', '.', $dhuhrTime) : '--.--';
            @endphp

            <div class="fs-6 mb-3">السلام عليكم ورحمة الله وبركاته</div>

            <div class="fs-6 mb-3">
                &#128197; Hari/Tgl : <strong>{{ $dayLabel }}</strong> | {{ $hijriDisplay }}
            </div>
            <div class="fs-6 mb-2 d-flex align-items-center gap-2">
                <span class="agenda-icon" aria-hidden="true">&#128115;&#8205;&#9794;&#65039;</span>
                <span>Khotib : <strong>{{ $schedule->khotib_name ?? '-' }}</strong></span>
            </div>
            <div class="fs-6 mb-2 d-flex align-items-center gap-2">
                <span class="agenda-icon" aria-hidden="true">
                    <svg viewBox="0 0 64 64" width="22" height="22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="32" cy="22" r="10" fill="#5c6b7a"/>
                        <path d="M16 54c2-10 10-18 16-18s14 8 16 18H16Z" fill="#5c6b7a"/>
                    </svg>
                </span>
                <span> Bilal : <strong>{{ $schedule->bilal ?? 'Bp. Adi' }}</strong></span>
            </div>
            <div class="fs-6 mb-4 d-flex align-items-center gap-2">
                &#128227; WAKTU ZUHUR : <strong>{{ $dhuhrDisplay }} WIB</strong>
            </div>

            <div class="mt-4">
                <p class="mb-2">INFAQ dan Shodaqoh dapat disalurkan melalui Bank Syariah Indonesia</p>
                <div class="fw-semibold">No Rek : 7126720909</div>
                <div class="fw-semibold mb-3">a/n MASJID ABU MUSA AL ASY'ARI</div>
                <div class="mt-3">والسلام عليكم ورحمة الله وبركاته</div>
                <div class="mt-3">
                    <a href="https://maps.app.goo.gl/QBiFWNgz5YQmHeb47" target="_blank" rel="noopener">https://maps.app.goo.gl/QBiFWNgz5YQmHeb47</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
