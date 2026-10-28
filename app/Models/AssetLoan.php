<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetLoan extends Model
{
    protected $fillable = [
        'asset_id',
        'borrower_name',
        'borrower_department',
        'borrower_contact',
        'loan_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'purpose',
        'return_notes',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
    ];

    protected function status(): Attribute
    {
        return Attribute::get(function (?string $value): string {
            $value ??= 'dipinjam';

            if ($value === 'dipinjam' && $this->expected_return_date?->isBefore(today())) {
                return 'terlambat';
            }

            return $value;
        });
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
