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
                    @php
                        $lastLogin = auth()->user()?->previous_login_at ?? auth()->user()?->last_login_at;
                        $lastLoginLabel = $lastLogin
                            ? \Carbon\Carbon::parse($lastLogin)->translatedFormat('l, d F Y H:i') . ' WIB'
                            : '-';
                    @endphp
                    <p class="text-muted mb-0">Last Login: {{ $lastLoginLabel }}</p>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary">Keluar</button>
                </form>
            </div>
            <hr class="my-3">
            <div class="d-flex flex-column flex-md-row gap-2">
                <a href="{{ route('admin.khotib-schedules.index') }}" class="btn btn-success">Kelola Jadwal Khotib</a>
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary">Lihat Log Aktivitas</a>
                <span class="text-muted align-self-center">Halaman ini siap diisi fitur pengelolaan agenda & pengumuman.</span>
            </div>
        </div>
    </div>
</div>
@endsection
