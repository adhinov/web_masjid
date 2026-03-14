<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackOnlineSessions
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (! $request->hasSession()) {
            return $response;
        }

        $session = $request->session();

        if (! $session->isStarted()) {
            return $response;
        }

        try {
            $ip = (string) $request->ip();
            $ua = (string) $request->userAgent();
            $key = sha1($ip . '|' . $ua);

            if ($key !== '') {
                $now = now()->getTimestamp();
                DB::table('online_sessions')->updateOrInsert(
                    ['session_id' => $key],
                    ['last_activity' => $now]
                );

                // Lightweight cleanup to prevent unbounded growth.
                if (random_int(1, 100) <= 3) {
                    $staleThreshold = now()->subHours(24)->getTimestamp();
                    DB::table('online_sessions')
                        ->where('last_activity', '<', $staleThreshold)
                        ->delete();
                }
            }
        } catch (\Throwable $e) {
            // Ignore tracking errors to avoid breaking user requests.
        }

        return $response;
    }
}
