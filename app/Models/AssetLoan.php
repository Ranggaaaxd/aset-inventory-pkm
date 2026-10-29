<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

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
        'asset_status_before_loan',
        'purpose',
        'return_notes',
    ];

    protected $attributes = [
        'status' => 'dipinjam',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (AssetLoan $loan): void {
            if (($loan->getAttributes()['status'] ?? 'dipinjam') !== 'dipinjam') {
                return;
            }

            $asset = Asset::query()->find($loan->asset_id);

            if (! $asset || ! Asset::query()->loanable()->whereKey($asset->getKey())->exists()) {
                throw ValidationException::withMessages([
                    'asset_id' => 'Aset ini tidak memenuhi syarat untuk dipinjam.',
                ]);
            }

            $loan->asset_status_before_loan = $asset->status;
            $loan->setRelation('asset', $asset);
        });

        static::created(function (AssetLoan $loan): void {
            if (($loan->getAttributes()['status'] ?? null) !== 'dipinjam') {
                return;
            }

            $loan->asset?->update(['status' => 'Dipinjam']);
        });

        static::updated(function (AssetLoan $loan): void {
            if (! $loan->wasChanged('status') || ($loan->getAttributes()['status'] ?? null) !== 'dikembalikan') {
                return;
            }

            $asset = $loan->asset;

            if (! $asset) {
                return;
            }

            $asset->update([
                'status' => $loan->asset_status_before_loan ?? 'Tersedia di Gudang',
            ]);
        });
    }

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
