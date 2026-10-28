<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'asset_code',
        'name',
        'category_id',
        'room_id',
        'pic_name',
        'pic_contact',
        'condition',
        'brand',
        'purchase_date',
        'price',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'price' => 'decimal:2',
    ];

    /**
     * Get the category that owns the asset.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the room where the asset is placed.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Label representation for activity audit logs.
     */
    public function getActivityRecordLabel(): string
    {
        return "{$this->asset_code} - {$this->name}";
    }
}

