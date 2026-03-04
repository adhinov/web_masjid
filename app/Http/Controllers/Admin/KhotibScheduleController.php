<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KhotibSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KhotibScheduleController extends Controller
{
    public function index()
    {
        $schedules = KhotibSchedule::query()
            ->get()
            ->sortBy(function (KhotibSchedule $schedule) {
                $dates = $schedule->khutbah_dates ?? [];
                return $dates ? min($dates) : '9999-12-31';
            })
            ->values();

        return view('admin.khotib-schedules.index', compact('schedules'));
    }

    public function create()
    {
        return view('admin.khotib-schedules.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        KhotibSchedule::create($data);

        return redirect()
            ->route('admin.khotib-schedules.index')
            ->with('success', 'Jadwal khotib berhasil ditambahkan.');
    }

    public function edit(KhotibSchedule $khotibSchedule)
    {
        return view('admin.khotib-schedules.edit', [
            'schedule' => $khotibSchedule,
        ]);
    }

    public function update(Request $request, KhotibSchedule $khotibSchedule)
    {
        $data = $this->validateRequest($request);

        $khotibSchedule->update($data);

        return redirect()
            ->route('admin.khotib-schedules.index')
            ->with('success', 'Jadwal khotib berhasil diperbarui.');
    }

    public function destroy(KhotibSchedule $khotibSchedule)
    {
        $khotibSchedule->delete();

        return redirect()
            ->route('admin.khotib-schedules.index')
            ->with('success', 'Jadwal khotib berhasil dihapus.');
    }

    private function validateRequest(Request $request): array
    {
        $request->validate([
            'khotib_name' => ['required', 'string', 'max:255'],
            'bilal' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:255'],
            'khutbah_dates' => ['required', 'string'],
        ]);

        $dates = $this->parseDates($request->input('khutbah_dates'));

        if (empty($dates)) {
            throw ValidationException::withMessages([
                'khutbah_dates' => 'Format tanggal harus YYYY-MM-DD, satu tanggal per baris.',
            ]);
        }

        return [
            'khotib_name' => $request->input('khotib_name'),
            'bilal' => $request->input('bilal'),
            'notes' => $request->input('notes'),
            'khutbah_dates' => $dates,
        ];
    }

    private function parseDates(string $rawDates): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $rawDates);

        $dates = collect($lines)
            ->map(fn ($value) => trim($value))
            ->filter()
            ->unique()
            ->values()
            ->all();

        foreach ($dates as $date) {
            $parsed = Carbon::createFromFormat('Y-m-d', $date);
            if (!$parsed || $parsed->format('Y-m-d') !== $date) {
                return [];
            }
        }

        sort($dates);

        return $dates;
    }
}
