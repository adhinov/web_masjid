@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container my-4">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h2 class="text-masjid fw-semibold mb-1">Dashboard Admin</h2>
                    <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name ?? 'Admin' }}.</p>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary">Keluar</button>
                </form>
            </div>
            <hr class="my-3">
            <p class="mb-0">Halaman ini siap diisi fitur pengelolaan agenda & pengumuman.</p>
        </div>
    </div>
</div>
@endsection
