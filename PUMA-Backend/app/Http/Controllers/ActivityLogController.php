<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs with filters
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user:id,name,email');

        // Filter by action
        if ($request->has('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        // Filter by model
        if ($request->has('model') && $request->model !== 'all') {
            $query->where('model', $request->model);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search in description
        if ($request->has('search') && $request->search) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // Order by latest first
        $query->orderBy('created_at', 'desc');

        // Paginate
        $perPage = $request->get('per_page', 20);
        $logs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Get activity log statistics
     */
    public function stats()
    {
        $stats = [
            'total_activities' => ActivityLog::count(),
            'by_action' => ActivityLog::select('action', DB::raw('count(*) as count'))
                ->groupBy('action')
                ->get()
                ->pluck('count', 'action'),
            'by_model' => ActivityLog::select('model', DB::raw('count(*) as count'))
                ->groupBy('model')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->pluck('count', 'model'),
            'by_user' => ActivityLog::with('user:id,name')
                ->select('user_id', DB::raw('count(*) as count'))
                ->groupBy('user_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'user_id' => $item->user_id,
                        'user_name' => $item->user->name ?? 'Unknown',
                        'count' => $item->count,
                    ];
                }),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'this_week' => ActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => ActivityLog::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get recent activity logs
     */
    public function recent(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $logs = ActivityLog::with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Get logs by specific user
     */
    public function byUser($userId)
    {
        $logs = ActivityLog::with('user:id,name,email')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Get available models for filtering
     */
    public function models()
    {
        $models = ActivityLog::select('model')
            ->distinct()
            ->orderBy('model')
            ->pluck('model');

        return response()->json([
            'success' => true,
            'data' => $models,
        ]);
    }
}
