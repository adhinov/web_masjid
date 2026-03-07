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

        $logs = AdminActivityLog::query()
            ->with('user')
            ->latest()
            ->paginate(50);

        return view('admin.activity-logs.index', [
            'logs' => $logs,
        ]);
    }
}
