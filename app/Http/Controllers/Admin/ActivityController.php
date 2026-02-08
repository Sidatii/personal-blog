<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display the activity log.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('admin')->orderBy('created_at', 'desc');

        // Filter by action
        if ($action = $request->get('action')) {
            $query->forAction($action);
        }

        // Filter by admin
        if ($adminId = $request->get('admin_id')) {
            $query->where('admin_id', $adminId);
        }

        // Filter by model type
        if ($modelType = $request->get('model_type')) {
            $query->forModel($modelType);
        }

        // Filter by date range
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $activities = $query->paginate(25);
        $admins = Admin::orderBy('email')->get();

        return view('admin.activity.index', compact('activities', 'admins'));
    }
}
