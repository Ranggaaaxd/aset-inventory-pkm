<?php

namespace Tests\Feature;

use App\Models\Asset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AssetDepreciationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_calculates_straight_line_annual_depreciation_and_book_value(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 10, 28));

        $asset = Asset::create([
            'asset_code' => 'AST-DEP-001',
            'name' => 'Laptop',
            'condition' => 'Baik',
            'price' => 120000,
            'useful_life_years' => 4,
            'salvage_value' => 20000,
            'purchase_date' => today()->subYears(4),
        ]);

        $this->assertSame(25000.0, $asset->annual_depreciation);
        $this->assertSame(20000.0, $asset->current_book_value);
    }

    public function test_book_value_does_not_depreciate_before_the_purchase_date_or_below_residual_value(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 10, 28));

        $futurePurchase = Asset::create([
            'asset_code' => 'AST-DEP-002',
            'name' => 'Meja',
            'condition' => 'Baik',
            'price' => 800000,
            'useful_life_years' => 8,
            'salvage_value' => 50000,
            'purchase_date' => today()->addYear(),
        ]);

        $this->assertSame(800000.0, $futurePurchase->current_book_value);

        $noUsefulLife = Asset::create([
            'asset_code' => 'AST-DEP-003',
            'name' => 'Kursi',
            'condition' => 'Baik',
            'price' => 100000,
            'useful_life_years' => 0,
            'salvage_value' => 5000,
            'purchase_date' => today()->subYears(10),
        ]);

        $this->assertSame(0.0, $noUsefulLife->annual_depreciation);
        $this->assertSame(100000.0, $noUsefulLife->current_book_value);
    }
}
