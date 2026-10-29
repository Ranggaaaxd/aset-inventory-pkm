<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supply extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'item_code',
        'name',
        'category_id',
        'stock',
        'min_stock',
        'unit',
        'notes',
    ];

    protected $casts = [
        'stock' => 'integer',
        'min_stock' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Supply $supply): void {
            if (blank(trim((string) $supply->item_code))) {
                $supply->item_code = static::generateNextItemCode();
            }
        });
    }

    public static function generateNextItemCode(): string
    {
        $prefix = 'PKM-ATK-';
        $nextNumber = static::query()
            ->where('item_code', 'like', $prefix.'%')
            ->pluck('item_code')
            ->map(fn (string $code): int => preg_match('/^'.preg_quote($prefix, '/').'(\d+)$/', $code, $matches)
                ? (int) $matches[1]
                : 0)
            ->max() + 1;

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the category that owns the supply.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the stock mutations for the supply.
     */
    public function stockMutations(): HasMany
    {
        return $this->hasMany(StockMutation::class);
    }

    /**
     * Check if current stock is low (at or below min_stock).
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    /**
     * Label representation for activity audit logs.
     */
    public function getActivityRecordLabel(): string
    {
        return $this->item_code ? "{$this->item_code} - {$this->name}" : $this->name;
    }
}
