<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminActivityLogController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $filterDate = $request->query('date');
        $query = $this->buildLogQuery($filterDate);
        $logs = $query->latest()->take(15)->get();

        return view('admin.activity-logs.index', [
            'logs' => $logs,
            'filterDate' => $filterDate,
        ]);
    }

    public function downloadPlainText(Request $request)
    {
        Carbon::setLocale('id');

        $filterDate = $request->query('date');
        $query = $this->buildLogQuery($filterDate);
        $logs = $query->latest()->take(15)->get();

        $rows = [];
        foreach ($logs as $index => $log) {
            $rows[] = [
                'No' => (string) ($index + 1),
                'Admin' => (string) ($log->user->name ?? 'Admin'),
                'Email' => (string) ($log->user->email ?? '-'),
                'Aksi' => (string) (str_replace('_', ' ', $log->action)),
                'IP' => (string) ($log->ip_address ?? '-'),
                'Waktu' => (string) (($log->created_at?->translatedFormat('l, d F Y H:i') ?? '-') . ' WIB'),
            ];
        }

        $content = $this->renderPlainTextTable($rows, $filterDate);
        $filename = ($filterDate === 'today')
            ? 'log-aktivitas-admin-hari-ini.txt'
            : 'log-aktivitas-admin.txt';

        return response($content)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function buildLogQuery(?string $filterDate)
    {
        $query = AdminActivityLog::query()
            ->with('user')
            ->where('action', 'login');

        if ($filterDate === 'today') {
            $start = Carbon::now('Asia/Jakarta')->startOfDay()->setTimezone('UTC');
            $end = Carbon::now('Asia/Jakarta')->endOfDay()->setTimezone('UTC');
            $query->whereBetween('created_at', [$start, $end]);
        }

        return $query;
    }

    private function renderPlainTextTable(array $rows, ?string $filterDate): string
    {
        $headers = ['No', 'Admin', 'Email', 'Aksi', 'IP', 'Waktu'];
        $caps = [
            'No' => 3,
            'Admin' => 18,
            'Email' => 26,
            'Aksi' => 8,
            'IP' => 15,
            'Waktu' => 34,
        ];

        $widths = [];
        foreach ($headers as $header) {
            $widths[$header] = min($caps[$header], strlen($header));
        }

        foreach ($rows as $row) {
            foreach ($headers as $header) {
                $value = $row[$header] ?? '';
                $widths[$header] = min(
                    $caps[$header],
                    max($widths[$header], strlen($value))
                );
            }
        }

        $line = '+';
        foreach ($headers as $header) {
            $line .= str_repeat('-', $widths[$header] + 2) . '+';
        }

        $out = [];
        $title = ($filterDate === 'today')
            ? 'Log Aktivitas Admin - Hari Ini (15 Login Terakhir)'
            : 'Log Aktivitas Admin - 15 Login Terakhir';
        $out[] = $title;
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
            $rowLine = '|';
            foreach ($headers as $header) {
                $value = $row[$header] ?? '';
                $rowLine .= ' ' . $this->padCell($value, $widths[$header]) . ' |';
            }
            $out[] = $rowLine;
        }
        $out[] = $line;

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
