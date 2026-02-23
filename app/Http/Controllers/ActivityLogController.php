<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ensure only admin/webmaster can access
        $this->authorize('viewAny', ActivityLog::class);

        $query = ActivityLog::with('user');

        // Filter by User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by Date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->latest()->paginate(20);
        $users = User::orderBy('nama_lengkap')->get();
        $actions = ActivityLog::distinct()->pluck('action');

        return view('admin.activity_logs.index', compact('logs', 'users', 'actions'));
    }
}
