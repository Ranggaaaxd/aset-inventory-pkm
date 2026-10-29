<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory, LogsActivity;

    public const STATUSES = [
        'Tersedia di Gudang' => 'Tersedia di Gudang',
        'Digunakan' => 'Digunakan',
        'Dipinjam' => 'Dipinjam',
        'Rusak/Afkir' => 'Rusak/Afkir',
    ];

    protected $fillable = [
        'asset_code',
        'name',
        'size',
        'quantity',
        'status',
        'category_id',
        'room_id',
        'pic_name',
        'pic_contact',
        'condition',
        'brand',
        'image',
        'tax_group',
        'purchase_date',
        'warranty_expiry_date',
        'next_maintenance_date',
        'price',
        'useful_life_years',
        'salvage_value',
        'notes',
    ];

    protected $attributes = [
        'quantity' => 1,
        'status' => 'Tersedia di Gudang',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry_date' => 'date',
        'next_maintenance_date' => 'date',
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'useful_life_years' => 'integer',
        'salvage_value' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Asset $asset): void {
            if (blank(trim((string) $asset->asset_code))) {
                $asset->asset_code = static::generateNextAssetCode();
            }
        });
    }

    public static function generateNextAssetCode(): string
    {
        $prefix = 'PKM-AST-'.now()->format('Y').'-';
        $nextNumber = static::query()
            ->where('asset_code', 'like', $prefix.'%')
            ->pluck('asset_code')
            ->map(fn (string $code): int => preg_match('/^'.preg_quote($prefix, '/').'(\d+)$/', $code, $matches)
                ? (int) $matches[1]
                : 0)
            ->max() + 1;

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function scopeLoanable(Builder $query): Builder
    {
        return $query
            ->where('condition', '!=', 'Rusak/Afkir')
            ->where('status', '!=', 'Dipinjam')
            ->where(function (Builder $query): void {
                $query->whereNull('pic_name')
                    ->orWhere('pic_name', '')
                    ->orWhere('pic_name', 'Barang Bersama');
            })
            ->where(function (Builder $query): void {
                $query->where('status', 'Tersedia di Gudang')
                    ->orWhere('status', 'Digunakan');
            });
    }

    public function getAnnualDepreciationAttribute(): float
    {
        $price = (float) ($this->price ?? 0);
        $salvageValue = min((float) ($this->salvage_value ?? 0), $price);
        $usefulLifeYears = (int) ($this->useful_life_years ?? 0);

        if ($usefulLifeYears <= 0) {
            return 0.0;
        }

        return round(max(0, $price - $salvageValue) / $usefulLifeYears, 2);
    }

    public function getCurrentBookValueAttribute(): float
    {
        $price = (float) ($this->price ?? 0);

        if ($price <= 0 || ! $this->purchase_date) {
            return $price;
        }

        $salvageValue = min((float) ($this->salvage_value ?? 0), $price);
        $elapsedDays = max(0, $this->purchase_date->diffInDays(today(), false));
        $depreciation = $this->annual_depreciation * ($elapsedDays / 365.25);

        return round(max($salvageValue, $price - $depreciation), 2);
    }

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

    public function loans(): HasMany
    {
        return $this->hasMany(AssetLoan::class);
    }

    /**
     * Label representation for activity audit logs.
     */
    public function getActivityRecordLabel(): string
    {
        return "{$this->asset_code} - {$this->name}";
    }
}
