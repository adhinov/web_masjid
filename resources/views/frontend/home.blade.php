@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="container my-3 hero-container">
    <div class="card shadow-sm border-0">
        <div class="card-body py-5 px-4">

            <div class="row align-items-start hero-row">

                <!-- Logo -->
                <div class="col-md-4 text-center mb-4 mb-md-0 hero-logo-col">
                    <img
                        src="{{ asset('images/logo-masjid.png') }}"
                        alt="Logo Masjid"
                        class="img-fluid hero-logo"
                    >
                </div>

                <!-- Text Content -->
                <div class="col-md-8">
                    <h2 class="text-masjid fw-semibold mb-3 hero-title">
                        Selamat Datang di Website Masjid
                    </h2>

                    <p class="mb-2 hero-subtitle">
                        Pusat informasi kegiatan, jadwal sholat, dan layanan jemaah.
                    </p>

                    <!-- Tanggal Hijriyah -->
                    <p class="text-muted mb-3 hero-date">
                        {{ $tanggalHijriyah }}
                    </p>

                    <!-- Countdown -->
                    <div id="countdown" class="alert alert-success fw-bold py-2 countdown-box"></div>

                    <!-- Garis Horizontal -->
                    

                    @php
                        \Carbon\Carbon::setLocale('id');
                        $tanggalMasehi = \Carbon\Carbon::now()->translatedFormat('l, d F Y');
                    @endphp

                    <p class="text-center fw-semibold mb-3 prayer-heading">
                        Waktu Sholat Kabupaten Bogor - {{ $tanggalMasehi }}
                    </p>

                    <!-- Jadwal Sholat Dinamis -->
                    <div class="row text-center g-3">

                        @foreach($jadwal as $nama => $waktu)
                        <div class="col-6 col-md">
                            <div class="p-3 waktu-card"
                                 data-time="{{ substr($waktu,0,5) }}"
                                 >
                                <strong class="prayer-label">
                                    {{ $nama }}
                                </strong><br>
                                <span class="prayer-time">
                                    {{ substr($waktu,0,5) }}
                                </span>
                            </div>
                        </div>
                        @endforeach

                    </div>

                </div>

            </div>

        </div>
    </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const cards = document.querySelectorAll(".waktu-card");
    const countdownEl = document.getElementById("countdown");

    if (!cards.length || !countdownEl) return;

    function isValidTime(value) {
        return /^\d{2}:\d{2}$/.test(value);
    }

    function getNextPrayer() {
        const now = new Date();
        let nextPrayer = null;
        let nextName = "";

        cards.forEach(card => {
            const time = card.dataset.time;
            if (!time || !isValidTime(time)) return;

            const [hours, minutes] = time.split(":");
            const prayerTime = new Date();
            prayerTime.setHours(parseInt(hours), parseInt(minutes), 0, 0);

            if (prayerTime > now && !nextPrayer) {
                nextPrayer = prayerTime;
                nextName = card.querySelector("strong").innerText.trim();
            }
        });

        // Kalau semua sudah lewat → ke waktu pertama besok
        if (!nextPrayer) {
            const firstCard = Array.from(cards).find(card => isValidTime(card.dataset.time));
            if (!firstCard) return { nextPrayer: null, nextName: "" };
            const time = firstCard.dataset.time;
            const [hours, minutes] = time.split(":");

            nextPrayer = new Date();
            nextPrayer.setDate(nextPrayer.getDate() + 1);
            nextPrayer.setHours(parseInt(hours), parseInt(minutes), 0, 0);

            nextName = firstCard.querySelector("strong").innerText.trim();
        }

        return { nextPrayer, nextName };
    }

    function updateCountdown() {
        const { nextPrayer, nextName } = getNextPrayer();
        if (!nextPrayer) {
            countdownEl.innerHTML = "Jadwal sholat belum tersedia";
            return;
        }

        const now = new Date();
        const diff = nextPrayer - now;

        if (diff <= 0) return;

        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff / (1000 * 60)) % 60);
        const seconds = Math.floor((diff / 1000) % 60);

        countdownEl.innerHTML =
        "Menuju <strong>" + nextName + "</strong> : " +
        hours + " jam " +
        minutes + " menit " +
        seconds + " detik";

        highlightCard(nextName);
    }

    function highlightCard(nextName) {
        cards.forEach(card => {
            card.style.background = "#f4d03f";
            card.style.color = "#000";

            if (card.querySelector("strong").innerText.trim() === nextName) {
                card.style.background = "#198754";
                card.style.color = "#fff";
            }
        });
    }

    // 🔥 INI YANG PENTING
    updateCountdown();              // panggil pertama kali
    setInterval(updateCountdown, 1000);

});
</script>
@endsection

