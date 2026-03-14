<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $onlineCount = null;
        $driver = config('session.driver');

        // Count only recently active sessions to avoid stale "online" users.
        $windowMinutes = 5;
        $threshold = now()->subMinutes($windowMinutes)->getTimestamp();

        try {
            $onlineCount = DB::table('online_sessions')
                ->where('last_activity', '>=', $threshold)
                ->count();
        } catch (\Throwable $e) {
            // Fall back to driver-based counting when the tracking table doesn't exist.
        }

        try {
            if ($onlineCount !== null) {
                return view('admin.dashboard', [
                    'onlineCount' => $onlineCount,
                ]);
            }

            if ($driver === 'database') {
                $table = config('session.table', 'sessions');
                $onlineCount = DB::table($table)
                    ->where('last_activity', '>=', $threshold)
                    ->count();
            } elseif ($driver === 'file') {
                $path = (string) config('session.files', storage_path('framework/sessions'));
                if ($path !== '' && File::isDirectory($path)) {
                    $onlineCount = collect(File::files($path))
                        ->filter(fn ($file) => $file->getMTime() >= $threshold)
                        ->count();
                }
            }
        } catch (\Throwable $e) {
            $onlineCount = null;
        }

        return view('admin.dashboard', [
            'onlineCount' => $onlineCount,
        ]);
    }
}
