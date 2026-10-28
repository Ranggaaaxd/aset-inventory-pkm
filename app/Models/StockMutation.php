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
     * Bootstrap the model and its traits.
     */
    protected static function booted(): void
    {
        static::creating(function (StockMutation $mutation) {
            $supply = $mutation->supply ?? Supply::find($mutation->supply_id);
            if ($supply) {
                $mutation->stock_before = $supply->stock;
                if ($mutation->type === 'in') {
                    $mutation->stock_after = $supply->stock + $mutation->quantity;
                } else {
                    $mutation->stock_after = max(0, $supply->stock - $mutation->quantity);
                }
                $supply->stock = $mutation->stock_after;
                $supply->save();
            }
        });
    }

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
