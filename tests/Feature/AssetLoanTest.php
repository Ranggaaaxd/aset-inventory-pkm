<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetLoan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
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
        $this->assertSame('Dipinjam', $asset->fresh()->status);
    }

    public function test_returning_a_loan_restores_asset_status_based_on_pic(): void
    {
        $assetWithPic = Asset::create([
            'asset_code' => 'AST-RETURN-001',
            'name' => 'Laptop',
            'pic_name' => 'Barang Bersama',
            'status' => 'Digunakan',
            'condition' => 'Baik',
        ]);
        $assetWithoutPic = Asset::create([
            'asset_code' => 'AST-RETURN-002',
            'name' => 'Proyektor',
            'condition' => 'Baik',
        ]);

        $loanWithPic = AssetLoan::create([
            'asset_id' => $assetWithPic->id,
            'borrower_name' => 'Siti Aminah',
            'expected_return_date' => today()->addWeek(),
        ]);
        $loanWithoutPic = AssetLoan::create([
            'asset_id' => $assetWithoutPic->id,
            'borrower_name' => 'Andi Wijaya',
            'expected_return_date' => today()->addWeek(),
        ]);

        $this->assertSame('Dipinjam', $assetWithPic->fresh()->status);
        $this->assertSame('Dipinjam', $assetWithoutPic->fresh()->status);

        $loanWithPic->update(['status' => 'dikembalikan']);
        $loanWithoutPic->update(['status' => 'dikembalikan']);

        $this->assertSame('Digunakan', $assetWithPic->fresh()->status);
        $this->assertSame('Tersedia di Gudang', $assetWithoutPic->fresh()->status);
    }

    public function test_only_eligible_assets_are_loanable(): void
    {
        $eligibleWarehouseAsset = Asset::create([
            'asset_code' => 'AST-LOAN-WAREHOUSE',
            'name' => 'Cadangan Gudang',
            'status' => 'Tersedia di Gudang',
            'condition' => 'Baik',
        ]);
        $eligibleSharedAsset = Asset::create([
            'asset_code' => 'AST-LOAN-SHARED',
            'name' => 'Proyektor Bersama',
            'status' => 'Digunakan',
            'pic_name' => 'Barang Bersama',
            'condition' => 'Rusak Ringan',
        ]);

        foreach ([
            [
                'asset_code' => 'AST-LOAN-PERSONAL',
                'status' => 'Tersedia di Gudang',
                'pic_name' => 'Budi Santoso',
            ],
            [
                'asset_code' => 'AST-LOAN-USED-PERSONAL',
                'status' => 'Digunakan',
                'pic_name' => 'Siti Aminah',
            ],
            [
                'asset_code' => 'AST-LOAN-BORROWED',
                'status' => 'Dipinjam',
            ],
            [
                'asset_code' => 'AST-LOAN-AFKIR',
                'status' => 'Rusak/Afkir',
            ],
        ] as $attributes) {
            Asset::create([
                'name' => $attributes['asset_code'],
                'condition' => 'Baik',
                ...$attributes,
            ]);
        }

        $this->assertEqualsCanonicalizing(
            [$eligibleWarehouseAsset->id, $eligibleSharedAsset->id],
            Asset::query()->loanable()->pluck('id')->all(),
        );
    }

    public function test_personally_assigned_assets_cannot_be_loaned_even_if_created_directly(): void
    {
        $asset = Asset::create([
            'asset_code' => 'AST-LOAN-PERSONAL-DIRECT',
            'name' => 'Laptop Staf',
            'status' => 'Digunakan',
            'pic_name' => 'Budi Santoso',
            'condition' => 'Baik',
        ]);

        try {
            AssetLoan::create([
                'asset_id' => $asset->id,
                'borrower_name' => 'Siti Aminah',
                'expected_return_date' => today()->addWeek(),
            ]);
            $this->fail('Aset yang dipegang PIC perorangan seharusnya tidak dapat dipinjam.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('asset_id', $exception->errors());
        }

        $this->assertSame('Digunakan', $asset->fresh()->status);
        $this->assertDatabaseMissing('asset_loans', ['asset_id' => $asset->id]);
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
