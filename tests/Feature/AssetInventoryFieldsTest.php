<?php

namespace Tests\Feature;

use App\Models\Asset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetInventoryFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_size_quantity_and_status_can_be_saved_with_expected_defaults(): void
    {
        $asset = Asset::create([
            'asset_code' => 'PKM-AST-UNIFORM-001',
            'name' => 'Seragam Kantor',
            'size' => 'XXXL',
            'quantity' => 3,
            'status' => 'Tersedia di Gudang',
            'condition' => 'Baik',
        ]);

        $this->assertSame('XXXL', $asset->size);
        $this->assertSame(3, $asset->quantity);
        $this->assertSame('Tersedia di Gudang', $asset->status);

        $defaultAsset = Asset::create([
            'asset_code' => 'PKM-AST-DEFAULT-001',
            'name' => 'Kursi Kantor',
            'condition' => 'Baik',
        ]);

        $this->assertNull($defaultAsset->size);
        $this->assertSame(1, $defaultAsset->quantity);
        $this->assertSame('Tersedia di Gudang', $defaultAsset->status);
    }
}
