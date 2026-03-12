<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $onlineCount = null;

        if (config('session.driver') === 'database') {
            $table = config('session.table', 'sessions');
            // Count only recently active sessions to avoid stale "online" users.
            $windowMinutes = 5;
            $threshold = now()->subMinutes($windowMinutes)->getTimestamp();

            try {
                $onlineCount = DB::table($table)
                    ->where('last_activity', '>=', $threshold)
                    ->count();
            } catch (\Throwable $e) {
                $onlineCount = null;
            }
        }

        return view('admin.dashboard', [
            'onlineCount' => $onlineCount,
        ]);
    }
}
