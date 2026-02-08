<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Log an admin action.
     */
    public function log(string $action, ?Model $model = null, ?string $description = null): ActivityLog
    {
        $admin = Auth::guard('admin')->user();

        $data = [
            'admin_id' => $admin->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        if ($model) {
            $data['model_type'] = get_class($model);
            $data['model_id'] = $model->id;
        }

        return ActivityLog::create($data);
    }

    /**
     * Get recent activity logs.
     */
    public function getRecent(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::recent($limit)->get();
    }

    /**
     * Get activity for a specific model.
     */
    public function getForModel(string $modelType, ?int $modelId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = ActivityLog::forModel($modelType)
            ->with('admin')
            ->orderBy('created_at', 'desc');

        if ($modelId) {
            $query->where('model_id', $modelId);
        }

        return $query->get();
    }
}
