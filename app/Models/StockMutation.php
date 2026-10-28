<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'supply_id',
        'room_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'notes',
        'mutation_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
        'mutation_date' => 'date',
    ];

    /**
     * Get the supply item for this mutation.
     */
    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }

    /**
     * Get the destination room for this mutation (if applicable).
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}

