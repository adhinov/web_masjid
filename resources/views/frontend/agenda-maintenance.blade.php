@extends('layouts.app')

@section('title', 'Agenda')

@section('content')
<div class="container my-4 agenda-maintenance">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="zakat-card">
                <div class="text-center mb-3">
                    <h2 class="text-masjid fw-semibold mb-2">ZAKAT FITRAH</h2>
                    <p class="text-muted mb-0 fs-6">
                        Panitia Zakat Fitrah Masjid Abi Musa Al Asy'ari saat ini sudah siap menerima serta menyalurkan
                        Zakat Fitrah Anda. Ketentuan berapa nilai yang harus dibayar akan diinformasikan lebih lanjut,
                        atau bisa langsung hubungi No Whatsapp berikut:
                    </p>
                </div>

                <div class="d-flex justify-content-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-whatsapp text-success fs-5"></i>
                        <a class="text-decoration-none fw-semibold" href="https://wa.me/628569022290" target="_blank" rel="noopener">
                            Asep Tarjana 08569022290
                        </a>
                    </div>
                </div>
            </div>

            <div class="kajian-divider"></div>

            <div class="kajian-section">
                <div class="kajian-header">
                    <div class="kajian-title">KAJIAN RUTIN</div>
                    <div class="kajian-note">(Saat ini sedang diliburkan karena Ramadhan)</div>
                    <div class="kajian-subtitle">Kajian Kitab NASHOIHUDDINIYYAH</div>
                </div>

                <div class="kajian-card">
                    <div class="kajian-row">
                        <div class="kajian-label">Waktu</div>
                        <div class="kajian-value">Rutin Setiap Selasa Malam Rabu</div>
                    </div>
                    <div class="kajian-row">
                        <div class="kajian-label">Pukul</div>
                        <div class="kajian-value">19.30 WIB (Ba'da Isya)</div>
                    </div>
                    <div class="kajian-row">
                        <div class="kajian-label">Bersama</div>
                        <div class="kajian-value">
                            <ul class="kajian-speakers">
                                <li>al habib Abdullah bin Abubakar Alaydrus</li>
                                <li>KH. Syukron Muzaki</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="kajian-separator"></div>

                <div class="kajian-header">
                    <div class="kajian-subtitle">DZIKIR MANAKIB</div>
                </div>

                <div class="kajian-card">
                    <div class="kajian-row">
                        <div class="kajian-label">Waktu</div>
                        <div class="kajian-value">Rutin Setiap Hari Minggu Malam Senin</div>
                    </div>
                    <div class="kajian-row">
                        <div class="kajian-label">Pukul</div>
                        <div class="kajian-value">19.30 WIB (Ba'da Isya)</div>
                    </div>
                    <div class="kajian-row">
                        <div class="kajian-label">Bersama</div>
                        <div class="kajian-value">al ustadz Mahfudin</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
