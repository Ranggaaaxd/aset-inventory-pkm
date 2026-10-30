<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDisposal extends Model
{
    protected $fillable = [
        'asset_id',
        'disposal_date',
        'disposal_method',
        'disposal_amount',
        'reason',
        'approved_by',
    ];

    protected $casts = [
        'disposal_date' => 'date',
        'disposal_amount' => 'integer',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
