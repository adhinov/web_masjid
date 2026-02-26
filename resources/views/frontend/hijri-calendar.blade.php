@extends('layouts.app')

@section('title', 'Kalender Hijriyah')

@section('content')
<div class="container my-4">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-2">
                <div>
                    <h2 class="text-masjid fw-semibold mb-1">Kalender Hijriyah</h2>
                    <p class="text-muted mb-0">Tampilan tanggal hijriyah untuk bulan berjalan.</p>
                </div>
                <div class="hijri-month text-masjid fw-semibold">
                    {{ $hijriMonthLabel }}
                </div>
            </div>

            <div class="hijri-calendar">
                <div class="calendar-header">
                    <div>Ahad</div>
                    <div>Senin</div>
                    <div>Selasa</div>
                    <div>Rabu</div>
                    <div>Kamis</div>
                    <div>Jumat</div>
                    <div>Sabtu</div>
                </div>
                <div class="calendar-grid" id="calendar-grid">
                    @php
                        $now = \Carbon\Carbon::now();
                        $firstDay = $now->copy()->startOfMonth();
                        $daysInMonth = $now->daysInMonth;
                        $startWeekday = $firstDay->dayOfWeek; // 0 = Ahad
                    @endphp

                    @for ($i = 0; $i < $startWeekday; $i++)
                        <div class="calendar-cell is-empty"></div>
                    @endfor

                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $isToday = $day === (int) $now->format('j');
                        @endphp
                        <div class="calendar-cell {{ $isToday ? 'is-today' : '' }}">
                            <div class="greg-day">{{ $day }}</div>
                            <div class="hijri-day">{{ $hijriDays[$day] ?? '-' }}</div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
