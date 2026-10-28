<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'loggable_type',
        'loggable_id',
        'record_label',
        'old_values',
        'new_values',
        'changed_fields',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user who performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent loggable model (Asset or Supply).
     */
    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get human-readable action label.
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created' => 'Data Ditambahkan',
            'updated' => 'Data Diperbarui',
            'deleted' => 'Data Dihapus',
            default => ucfirst($this->action),
        };
    }

    /**
     * Get action badge color.
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get human-readable model label.
     */
    public function getModelLabelAttribute(): string
    {
        return match (class_basename($this->loggable_type)) {
            'Asset' => 'Aset Kantor',
            'Supply' => 'Perlengkapan (ATK)',
            default => class_basename($this->loggable_type),
        };
    }
}

