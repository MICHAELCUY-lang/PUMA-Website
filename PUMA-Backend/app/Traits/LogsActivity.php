<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Boot the trait
     */
    protected static function bootLogsActivity()
    {
        // Log when model is created
        static::created(function ($model) {
            $model->logActivity('create', 'Created ' . class_basename($model) . ': ' . $model->getLogDescription(), null, $model->getLogAttributes());
        });

        // Log when model is updated
        static::updated(function ($model) {
            $changes = $model->getChanges();
            if (!empty($changes) && !isset($changes['updated_at'])) {
                $model->logActivity('update', 'Updated ' . class_basename($model) . ': ' . $model->getLogDescription(), $model->getOriginal(), $changes);
            }
        });

        // Log when model is deleted
        static::deleted(function ($model) {
            $model->logActivity('delete', 'Deleted ' . class_basename($model) . ': ' . $model->getLogDescription(), $model->getAttributes(), null);
        });
    }

    /**
     * Log the activity
     */
    protected function logActivity($action, $description, $oldValues = null, $newValues = null)
    {
        // Skip logging if user is not authenticated
        if (!Auth::check()) {
            return;
        }

        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'model' => class_basename($this),
                'model_id' => $this->id ?? null,
                'description' => $description,
                'old_values' => $oldValues ? $this->filterLogAttributes($oldValues) : null,
                'new_values' => $newValues ? $this->filterLogAttributes($newValues) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently fail - don't break the main operation
            \Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }

    /**
     * Get description for log
     */
    protected function getLogDescription()
    {
        // Try common name fields
        if (isset($this->name)) {
            return $this->name;
        }
        if (isset($this->title)) {
            return $this->title;
        }
        if (isset($this->email)) {
            return $this->email;
        }
        
        return '#' . ($this->id ?? 'new');
    }

    /**
     * Get attributes to log
     */
    protected function getLogAttributes()
    {
        return $this->filterLogAttributes($this->getAttributes());
    }

    /**
     * Filter sensitive attributes from logs
     */
    protected function filterLogAttributes($attributes)
    {
        $hidden = ['password', 'remember_token', 'created_at', 'updated_at'];
        
        return array_diff_key($attributes, array_flip($hidden));
    }
}
