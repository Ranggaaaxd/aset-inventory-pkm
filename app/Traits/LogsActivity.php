<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Boot the LogsActivity trait for the model.
     */
    public static function bootLogsActivity(): void
    {
        static::created(function (Model $model) {
            self::recordActivity($model, 'created');
        });

        static::updated(function (Model $model) {
            self::recordActivity($model, 'updated');
        });

        static::deleted(function (Model $model) {
            self::recordActivity($model, 'deleted');
        });
    }

    /**
     * Record an activity log entry.
     */
    protected static function recordActivity(Model $model, string $action): void
    {
        $ignored = ['created_at', 'updated_at', 'remember_token'];
        $oldValues = null;
        $newValues = null;
        $changedFields = null;

        if ($action === 'created') {
            $newValues = collect($model->getAttributes())
                ->except($ignored)
                ->toArray();
        } elseif ($action === 'updated') {
            $dirty = collect($model->getDirty())->except($ignored)->toArray();

            if (empty($dirty)) {
                return;
            }

            $changedFields = array_keys($dirty);
            $oldValues = [];
            $newValues = [];

            foreach ($changedFields as $field) {
                $oldValues[$field] = $model->getOriginal($field);
                $newValues[$field] = $model->getAttribute($field);
            }
        } elseif ($action === 'deleted') {
            $oldValues = collect($model->getOriginal())
                ->except($ignored)
                ->toArray();
        }

        $user = Auth::user();
        $userName = $user?->name ?? (app()->runningInConsole() ? 'System / Seeder' : 'Admin PKM');

        $recordLabel = method_exists($model, 'getActivityRecordLabel')
            ? $model->getActivityRecordLabel()
            : ($model->name ?? (string) $model->getKey());

        ActivityLog::create([
            'user_id' => $user?->id,
            'user_name' => $userName,
            'action' => $action,
            'loggable_type' => get_class($model),
            'loggable_id' => $model->getKey(),
            'record_label' => $recordLabel,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changed_fields' => $changedFields,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    /**
     * Get activity logs for this model.
     */
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable')->orderBy('created_at', 'desc');
    }
}

