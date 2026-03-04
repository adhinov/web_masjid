@extends('layouts.app')

@section('title', 'Jadwal Khotib Jumat')

@section('content')
<div class="container my-4">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h2 class="text-masjid fw-semibold mb-1">Jadwal Khotib Jumat</h2>
                    <p class="text-muted mb-0">Kelola jadwal khotib Jumat per nama khotib.</p>
                </div>
                <a href="{{ route('admin.khotib-schedules.create') }}" class="btn btn-success">Tambah Jadwal</a>
            </div>

            @if (session('success'))
                <div class="alert alert-success mt-3 mb-0">{{ session('success') }}</div>
            @endif

            <div class="table-responsive mt-3">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Nama Khotib</th>
                            <th>Bilal</th>
                            <th>Tanggal Khutbah</th>
                            <th>Keterangan</th>
                            <th style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($schedules as $index => $schedule)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $schedule->khotib_name }}</td>
                                <td>{{ $schedule->bilal ?? 'Bp. Adi' }}</td>
                                <td>
                                    @php
                                        $dates = $schedule->khutbah_dates ?? [];
                                    @endphp
                                    @if (empty($dates))
                                        <span class="text-muted">-</span>
                                    @else
                                        <ul class="mb-0 ps-3">
                                            @foreach ($dates as $date)
                                                <li>{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                                <td>{{ $schedule->notes ?? '-' }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.khotib-schedules.edit', $schedule) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('admin.khotib-schedules.destroy', $schedule) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada jadwal khotib.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
