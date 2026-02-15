@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="container my-3">
    <div class="card shadow-sm border-0">
        <div class="card-body p-3">
            <h2 class="text-masjid fw-semibold mb-3 hero-title text-center">
                Mukadimah
            </h2>
            <img
                src="{{ asset('images/masjid.jpg') }}"
                alt="Foto Masjid"
                class="img-fluid masjid-photo"
            >
            <div class="mt-4">
                <h3 class="section-title mb-2">Sambutan Pengurus</h3>
                <p class="section-text mb-2">Assalamu’alaikum warahmatullahi wabarakatuh.</p>
                <p class="section-text mb-2">
                    Segala puji bagi Allah Subhanahu wa Ta’ala yang telah memberikan kita nikmat iman, islam, serta
                    kesempatan untuk terus memakmurkan rumah-Nya. Shalawat dan salam semoga tercurah kepada Nabi
                    Muhammad Shallallahu ‘alaihi wasallam, keluarga, sahabat, dan seluruh umatnya.
                </p>
                <p class="section-text mb-2">
                    Masjid Jami’ Abi Musa Al Asy’ari hadir sebagai pusat ibadah, dakwah, pendidikan, dan pembinaan umat
                    di lingkungan kita. Kami berkomitmen menjadikan masjid ini sebagai tempat yang nyaman, bersih, dan
                    menjadi pusat kegiatan keislaman yang membawa manfaat bagi masyarakat sekitar.
                </p>
                <p class="section-text mb-3">
                    Saat ini, masjid sedang dalam tahap renovasi dan penyempurnaan pembangunan agar dapat memberikan
                    fasilitas yang lebih baik bagi jamaah. InsyaAllah dengan dukungan dan doa dari seluruh kaum muslimin,
                    pembangunan ini dapat segera terselesaikan.
                </p>

                <h3 class="section-title mb-2">Open Donasi Pembangunan Masjid</h3>
                <p class="section-text mb-2">
                    Kami membuka kesempatan bagi Bapak/Ibu serta kaum muslimin untuk turut berpartisipasi dalam amal
                    jariyah pembangunan Masjid Jami’ Abi Musa Al Asy’ari.
                </p>
                <p class="section-text mb-2">
                    Setiap bantuan yang diberikan, sekecil apapun, insyaAllah menjadi pahala yang terus mengalir selama
                    masjid ini digunakan untuk shalat, dzikir, kajian, dan kegiatan kebaikan lainnya.
                </p>
                <p class="section-text mb-2">Rasulullah ﷺ bersabda:</p>
                <blockquote class="section-text mb-3">
                    “Barang siapa membangun masjid karena Allah, maka Allah akan membangunkan baginya rumah di surga.”
                    (HR. Bukhari & Muslim)
                </blockquote>

                <h3 class="section-title mb-2">Rekening Donasi</h3>
                <p class="section-text mb-1">No. Rekening:</p>
                <p class="section-text fw-semibold mb-2">7126720909</p>
                <p class="section-text mb-1">a/n:</p>
                <p class="section-text fw-semibold mb-3">MASJID ABU MUSA AL ASY'ARI</p>
                <p class="section-text mb-0">
                    Konfirmasi transfer dapat menghubungi nomor WhatsApp pengurus.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
