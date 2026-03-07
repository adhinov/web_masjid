@extends('layouts.app')

@section('title', 'Agenda')

@section('content')
<div class="container my-4">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
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
    </div>
</div>
@endsection
