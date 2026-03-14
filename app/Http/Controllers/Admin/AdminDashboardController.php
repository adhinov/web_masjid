<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $onlineCount = $this->getOnlineCount();

        return view('admin.dashboard', [
            'onlineCount' => $onlineCount,
        ]);
    }

    public function onlineCount()
    {
        return response()->json([
            'online' => $this->getOnlineCount() ?? 0,
        ]);
    }

    private function getOnlineCount(): ?int
    {
        $onlineCount = null;
        $driver = config('session.driver');

        // Count only very recent sessions so numbers drop quickly when users leave.
        $windowSeconds = 5;
        $threshold = now()->subSeconds($windowSeconds)->getTimestamp();

        try {
            $onlineCount = DB::table('online_sessions')
                ->where('last_activity', '>=', $threshold)
                ->count();
        } catch (\Throwable $e) {
            // Fall back to driver-based counting when the tracking table doesn't exist.
        }

        try {
            if ($onlineCount !== null) {
                return $onlineCount;
            }

            if ($driver === 'database') {
                $table = config('session.table', 'sessions');
                return DB::table($table)
                    ->where('last_activity', '>=', $threshold)
                    ->count();
            }

            if ($driver === 'file') {
                $path = (string) config('session.files', storage_path('framework/sessions'));
                if ($path !== '' && File::isDirectory($path)) {
                    return collect(File::files($path))
                        ->filter(fn ($file) => $file->getMTime() >= $threshold)
                        ->count();
                }
            }
        } catch (\Throwable $e) {
            return null;
        }

        return $onlineCount;
    }
}
