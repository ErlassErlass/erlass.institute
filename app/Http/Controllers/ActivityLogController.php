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
        // Ensure only webmaster / admin_sistem can access
        $this->authorize('viewAny', ActivityLog::class);

        $query = ActivityLog::with('user');

        // Role filter (Default: Pergerakan Admin & Webmaster Only)
        $roleFilter = $request->input('role_filter', 'admin_only');
        if ($roleFilter === 'admin_only') {
            $query->whereHas('user', function ($q) {
                $q->whereIn('role', ['webmaster', 'admin_sistem', 'admin']);
            });
        } elseif ($roleFilter !== 'all') {
            $query->whereHas('user', function ($q) use ($roleFilter) {
                $q->where('role', $roleFilter);
            });
        }

        // Filter by Specific User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Action Type
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Keyword Search (Description, Action, IP, User Name/Email)
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('description', 'LIKE', $searchTerm)
                  ->orWhere('action', 'LIKE', $searchTerm)
                  ->orWhere('ip_address', 'LIKE', $searchTerm)
                  ->orWhereHas('user', function ($uQ) use ($searchTerm) {
                      $uQ->where('nama_lengkap', 'LIKE', $searchTerm)
                         ->orWhere('email', 'LIKE', $searchTerm);
                  });
            });
        }

        // Date Filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Pagination with query string preservation
        $perPage = $request->input('per_page', 25);
        if ($perPage === 'all') {
            $totalCount = (clone $query)->count();
            $logs = $query->latest()->paginate(max($totalCount, 1000))->withQueryString();
        } else {
            $logs = $query->latest()->paginate((int) $perPage)->withQueryString();
        }

        $users = User::orderBy('nama_lengkap')->get();
        $adminUsers = User::whereIn('role', ['webmaster', 'admin_sistem', 'admin'])->orderBy('nama_lengkap')->get();
        $actions = ActivityLog::distinct()->pluck('action');

        return view('admin.activity_logs.index', compact('logs', 'users', 'adminUsers', 'actions', 'roleFilter'));
    }
}
