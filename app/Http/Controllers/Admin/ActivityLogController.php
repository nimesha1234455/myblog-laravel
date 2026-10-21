<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAdmin($request);

        $logs = ActivityLog::with('user')
            ->when($request->filled('action'), fn ($q) => $q->where('action', 'like', $request->action . '%'))
            ->when($request->filled('q'), fn ($q) => $q->where('description', 'like', '%' . $request->q . '%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.activity-logs', compact('logs'));
    }

    public function clear(Request $request)
    {
        $this->authorizeAdmin($request);

        ActivityLog::where('created_at', '<', now()->subDays(30))->delete();

        return back()->with('success', 'Logs older than 30 days cleared.');
    }

    /*
      Admin ta witharak allow. Role eka 'admin' nowe nam 403.
     */
    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }
}