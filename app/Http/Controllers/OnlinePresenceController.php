<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnlinePresenceController extends Controller
{
    public function ping(Request $request)
    {
        $key = $this->buildKey($request);
        if ($key === '') {
            return response()->noContent();
        }

        try {
            DB::table('online_sessions')->updateOrInsert(
                ['session_id' => $key],
                ['last_activity' => now()->getTimestamp()]
            );
        } catch (\Throwable $e) {
            // ignore
        }

        return response()->noContent();
    }

    public function leave(Request $request)
    {
        $key = $this->buildKey($request);
        if ($key === '') {
            return response()->noContent();
        }

        try {
            DB::table('online_sessions')
                ->where('session_id', $key)
                ->delete();
        } catch (\Throwable $e) {
            // ignore
        }

        return response()->noContent();
    }

    private function buildKey(Request $request): string
    {
        $ip = (string) $request->ip();
        $ua = (string) $request->userAgent();

        return sha1($ip . '|' . $ua);
    }
}
