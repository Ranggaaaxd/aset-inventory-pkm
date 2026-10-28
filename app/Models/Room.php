<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'location_floor',
        'description',
    ];

    /**
     * Get the assets placed in this room.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * Get the stock mutations allocated to this room.
     */
    public function stockMutations(): HasMany
    {
        return $this->hasMany(StockMutation::class);
    }
}

