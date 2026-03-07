@extends('layouts.app')

@section('title', 'Log Aktivitas Admin')

@section('content')
<div class="container my-4">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                <div>
                    <h2 class="text-masjid fw-semibold mb-1">Log Aktivitas Admin</h2>
                    <p class="text-muted mb-0">Riwayat aktivitas terakhir admin.</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70px;">No</th>
                            <th>Admin</th>
                            <th>Aksi</th>
                            <th>Objek</th>
                            <th>IP</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $index => $log)
                        <tr>
                            <td>{{ $logs->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-semibold">{{ $log->user->name ?? 'Admin' }}</div>
                                <div class="text-muted small">{{ $log->user->email ?? '-' }}</div>
                            </td>
                            <td class="text-capitalize">{{ str_replace('_', ' ', $log->action) }}</td>
                            <td class="text-muted">
                                {{ $log->subject_type ? class_basename($log->subject_type) : '-' }}
                                @if($log->subject_id)
                                    #{{ $log->subject_id }}
                                @endif
                            </td>
                            <td class="text-muted">{{ $log->ip_address ?? '-' }}</td>
                            <td class="text-muted">
                                {{ $log->created_at?->translatedFormat('l, d F Y H:i') ?? '-' }} WIB
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada aktivitas tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
