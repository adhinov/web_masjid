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
        [$schedules, $monthMapId] = $this->buildScheduleCollection($search);

        return view('admin.khotib-schedules.index', [
            'schedules' => $schedules,
            'search' => $search,
            'monthMapId' => $monthMapId,
        ]);
    }

    public function downloadPlainText(Request $request)
    {
        Carbon::setLocale('id');
        $search = trim($request->query('q', ''));
        [$schedules, $monthMapId] = $this->buildScheduleCollection($search);

        $rows = [];
        foreach ($schedules as $index => $schedule) {
            $dates = $schedule->khutbah_dates ?? [];
            $dateLabels = collect($dates)
                ->map(function ($date) use ($monthMapId) {
                    try {
                        $dateObj = Carbon::createFromFormat('Y-m-d', $date);
                        $monthName = $monthMapId[$dateObj->month] ?? $dateObj->translatedFormat('F');
                        return sprintf('%02d %s %d', $dateObj->day, $monthName, $dateObj->year);
                    } catch (\Throwable $e) {
                        return $date;
                    }
                })
                ->values()
                ->all();

            $rows[] = [
                'No' => (string) ($index + 1),
                'Nama Khotib' => (string) $schedule->khotib_name,
                'Bilal' => (string) ($schedule->bilal ?? 'Bp. Adi'),
                'Tanggal Khutbah' => $dateLabels ?: ['-'],
                'Keterangan' => (string) ($schedule->notes ?? '-'),
            ];
        }

        $content = $this->renderPlainTextTable($rows, $search);
        $filename = $search !== ''
            ? 'jadwal-khotib-jumat-filter.txt'
            : 'jadwal-khotib-jumat.txt';

        return response($content)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
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

    private function buildScheduleCollection(string $search): array
    {
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

        return [$schedules, $monthMapId];
    }

    private function renderPlainTextTable(array $rows, string $search): string
    {
        $headers = ['No', 'Nama Khotib', 'Bilal', 'Tanggal Khutbah', 'Keterangan'];
        $caps = [
            'No' => 3,
            'Nama Khotib' => 40,
            'Bilal' => 16,
            'Tanggal Khutbah' => 28,
            'Keterangan' => 24,
        ];

        $widths = [];
        foreach ($headers as $header) {
            $widths[$header] = min($caps[$header], strlen($header));
        }

        foreach ($rows as $row) {
            foreach ($headers as $header) {
                $value = $row[$header] ?? '';
                if ($header === 'Tanggal Khutbah' && is_array($value)) {
                    foreach ($value as $dateLine) {
                        $widths[$header] = min(
                            $caps[$header],
                            max($widths[$header], strlen($dateLine))
                        );
                    }
                    continue;
                }
                $widths[$header] = min(
                    $caps[$header],
                    max($widths[$header], strlen((string) $value))
                );
            }
        }

        $line = '+';
        foreach ($headers as $header) {
            $line .= str_repeat('-', $widths[$header] + 2) . '+';
        }

        $out = [];
        $title = $search !== ''
            ? 'Jadwal Khotib Jumat (Filter: ' . $search . ')'
            : 'Jadwal Khotib Jumat';
        $downloadedAt = Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y H:i') . ' WIB';
        $out[] = $title;
        $out[] = 'Tanggal Download: ' . $downloadedAt;
        $out[] = $line;

        $headerRow = '|';
        foreach ($headers as $header) {
            $headerRow .= ' ' . $this->padCell($header, $widths[$header]) . ' |';
        }
        $out[] = $headerRow;
        $out[] = $line;

        if (empty($rows)) {
            $empty = '| ' . $this->padCell('Tidak ada data.', array_sum($widths) + (count($headers) * 3) - 3) . ' |';
            $out[] = $empty;
            $out[] = $line;
            return implode(PHP_EOL, $out) . PHP_EOL;
        }

        foreach ($rows as $row) {
            $dateLines = $row['Tanggal Khutbah'] ?? ['-'];
            if (!is_array($dateLines) || empty($dateLines)) {
                $dateLines = ['-'];
            }

            $lineCount = count($dateLines);
            for ($i = 0; $i < $lineCount; $i++) {
                $rowLine = '|';
                foreach ($headers as $header) {
                    if ($header === 'Tanggal Khutbah') {
                        $value = $dateLines[$i] ?? '';
                    } else {
                        $value = $i === 0 ? ($row[$header] ?? '') : '';
                    }
                    $rowLine .= ' ' . $this->padCell((string) $value, $widths[$header]) . ' |';
                }
                $out[] = $rowLine;
            }
            $out[] = $line;
        }

        return implode(PHP_EOL, $out) . PHP_EOL;
    }

    private function padCell(string $value, int $width): string
    {
        if (strlen($value) > $width) {
            $value = substr($value, 0, max(0, $width - 3)) . '...';
        }

        return str_pad($value, $width, ' ');
    }
}
