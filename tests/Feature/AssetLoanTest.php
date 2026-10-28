<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetLoan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetLoanTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_loan_uses_today_as_the_default_loan_date_and_belongs_to_an_asset(): void
    {
        $asset = Asset::create([
            'asset_code' => 'AST-001',
            'name' => 'Laptop',
            'condition' => 'Baik',
        ]);

        $loan = AssetLoan::create([
            'asset_id' => $asset->id,
            'borrower_name' => 'Budi Santoso',
            'expected_return_date' => today()->addWeek(),
        ]);

        $this->assertSame(today()->toDateString(), $loan->fresh()->loan_date->toDateString());
        $this->assertTrue($loan->asset->is($asset));
        $this->assertTrue($asset->loans()->whereKey($loan->id)->exists());
    }

    public function test_deleting_an_asset_cascades_to_its_loans(): void
    {
        $asset = Asset::create([
            'asset_code' => 'AST-002',
            'name' => 'Proyektor',
            'condition' => 'Baik',
        ]);

        $loan = AssetLoan::create([
            'asset_id' => $asset->id,
            'borrower_name' => 'Siti Aminah',
            'expected_return_date' => today()->addWeek(),
        ]);

        $asset->delete();

        $this->assertDatabaseMissing('asset_loans', ['id' => $loan->id]);
    }

    public function test_an_unreturned_loan_past_its_expected_return_date_is_overdue(): void
    {
        $asset = Asset::create([
            'asset_code' => 'AST-003',
            'name' => 'Kamera',
            'condition' => 'Baik',
        ]);

        $loan = AssetLoan::create([
            'asset_id' => $asset->id,
            'borrower_name' => 'Andi Wijaya',
            'expected_return_date' => today()->subDay(),
        ]);

        $this->assertSame('terlambat', $loan->status);
    }
}
