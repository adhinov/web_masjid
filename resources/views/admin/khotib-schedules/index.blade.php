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
                <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-2">
                    <form method="GET" action="{{ route('admin.khotib-schedules.index') }}" class="d-flex gap-2">
                        <input type="text" name="q" id="khotib-search" value="{{ $search ?? '' }}" class="form-control form-control-sm search-input" placeholder="Cari nama khotib/nama bulannya" autocomplete="off">
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Cari</button>
                    </form>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success mt-3 mb-0">{{ session('success') }}</div>
            @endif

            @php
                $monthMapId = $monthMapId ?? [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember',
                ];
            @endphp

            <div class="table-responsive mt-3">
                <table class="table table-bordered table-sm align-middle khotib-table">
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
                    <tbody id="khotib-table-body">
                        @forelse ($schedules as $index => $schedule)
                            <tr data-khotib="{{ strtolower($schedule->khotib_name) }}"
                                data-search="{{ $schedule->search_tokens ?? strtolower($schedule->khotib_name) }}">
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
                                        <ul class="mb-0 ps-3 khotib-dates">
                                            @foreach ($dates as $date)
                                                @php
                                                    try {
                                                        $dateObj = \Carbon\Carbon::createFromFormat('Y-m-d', $date);
                                                        $monthName = $monthMapId[$dateObj->month] ?? $dateObj->translatedFormat('F');
                                                        $displayDate = sprintf('%02d %s %d', $dateObj->day, $monthName, $dateObj->year);
                                                    } catch (\Throwable $e) {
                                                        $displayDate = $date;
                                                    }
                                                @endphp
                                                <li>{{ $displayDate }}</li>
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
            <script>
                (function () {
                    const input = document.getElementById('khotib-search');
                    const tbody = document.getElementById('khotib-table-body');
                    if (!input || !tbody) return;

                    const rows = Array.from(tbody.querySelectorAll('tr[data-khotib]'));
                    const emptyRow = tbody.querySelector('tr:not([data-khotib])');

                    const normalizeQuery = (value) => {
                        const q = value.trim().toLowerCase();
                        const map = {
                            january: 'januari',
                            february: 'februari',
                            march: 'maret',
                            april: 'april',
                            may: 'mei',
                            june: 'juni',
                            july: 'juli',
                            august: 'agustus',
                            september: 'september',
                            october: 'oktober',
                            november: 'november',
                            december: 'desember',
                        };
                        return map[q] || q;
                    };

                    const filterRows = () => {
                        const query = normalizeQuery(input.value);
                        let visibleCount = 0;

                        rows.forEach((row) => {
                            const tokens = row.getAttribute('data-search') || row.getAttribute('data-khotib') || '';
                            const match = query === '' || tokens.includes(query);
                            row.style.display = match ? '' : 'none';
                            if (match) visibleCount += 1;
                        });

                        if (emptyRow) {
                            emptyRow.style.display = visibleCount === 0 ? '' : 'none';
                        }
                    };

                    input.addEventListener('input', filterRows);
                    filterRows();
                })();
            </script>
            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('admin.khotib-schedules.create') }}" class="btn btn-success btn-sm">Tambah Jadwal</a>
                <a href="{{ route('admin.khotib-schedules.download', array_filter(['q' => $search ?? null])) }}"
                   class="btn btn-outline-success btn-sm">
                    Download Jadwal
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
