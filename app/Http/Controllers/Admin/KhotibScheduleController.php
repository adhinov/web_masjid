<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\KhotibSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class KhotibScheduleController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $search = trim($request->query('q', ''));
        $searchLower = mb_strtolower($search);

        $monthMapId = [
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
        $monthMapEn = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        $monthAlias = [
            'january' => 'januari',
            'february' => 'februari',
            'march' => 'maret',
            'april' => 'april',
            'may' => 'mei',
            'june' => 'juni',
            'july' => 'juli',
            'august' => 'agustus',
            'september' => 'september',
            'october' => 'oktober',
            'november' => 'november',
            'december' => 'desember',
        ];

        if (isset($monthAlias[$searchLower])) {
            $searchLower = $monthAlias[$searchLower];
        }

        $schedules = KhotibSchedule::all()
            ->map(function (KhotibSchedule $schedule) use ($monthMapId, $monthMapEn) {
                $dates = $schedule->khutbah_dates ?? [];
                $months = collect($dates)
                    ->map(function ($date) use ($monthMapId, $monthMapEn) {
                        try {
                            $month = Carbon::createFromFormat('Y-m-d', $date)->month;
                            $monthId = $monthMapId[$month] ?? null;
                            $monthEn = $monthMapEn[$month] ?? null;
                            return trim(($monthId ?? '') . ' ' . ($monthEn ?? '')) ?: null;
                        } catch (\Throwable $e) {
                            return null;
                        }
                    })
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                $tokens = strtolower(trim($schedule->khotib_name . ' ' . implode(' ', $months)));
                $schedule->setAttribute('search_tokens', $tokens);

                return $schedule;
            });

        if ($searchLower !== '') {
            $schedules = $schedules->filter(function (KhotibSchedule $schedule) use ($searchLower) {
                $tokens = $schedule->getAttribute('search_tokens') ?? '';
                return str_contains($tokens, $searchLower);
            });
        }

        $schedules = $schedules
            ->sortBy(function (KhotibSchedule $schedule) {
                $dates = $schedule->khutbah_dates ?? [];
                return $dates ? min($dates) : '9999-12-31';
            })
            ->values();

        return view('admin.khotib-schedules.index', [
            'schedules' => $schedules,
            'search' => $search,
            'monthMapId' => $monthMapId,
        ]);
    }

    public function create()
    {
        return view('admin.khotib-schedules.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $schedule = KhotibSchedule::create($data);

        $this->logActivity('khotib_schedule_created', $request, $schedule);

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

        $this->logActivity('khotib_schedule_updated', $request, $khotibSchedule);

        return redirect()
            ->route('admin.khotib-schedules.index')
            ->with('success', 'Jadwal khotib berhasil diperbarui.');
    }

    public function destroy(KhotibSchedule $khotibSchedule)
    {
        $khotibSchedule->delete();

        $this->logActivity('khotib_schedule_deleted', request(), $khotibSchedule);

        return redirect()
            ->route('admin.khotib-schedules.index')
            ->with('success', 'Jadwal khotib berhasil dihapus.');
    }

    private function logActivity(string $action, Request $request, ?KhotibSchedule $schedule = null): void
    {
        $user = Auth::user();

        AdminActivityLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'subject_type' => $schedule ? KhotibSchedule::class : null,
            'subject_id' => $schedule?->id,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);
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
