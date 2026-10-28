<?php

namespace Tests\Feature;

use App\Filament\Pages\ScanAsset;
use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanAssetTest extends TestCase
{
    use RefreshDatabase;

    public function test_scanned_asset_code_returns_a_safe_summary_and_edit_url(): void
    {
        $asset = Asset::create([
            'asset_code' => 'PKM-AST-001',
            'name' => 'Laptop',
            'condition' => 'Baik',
            'pic_name' => 'Budi Santoso',
        ]);

        $page = new ScanAsset;
        $page->lookupAsset('PKM-AST-001');

        $this->assertSame([
            'asset_code' => 'PKM-AST-001',
            'name' => 'Laptop',
            'room' => '-',
            'pic_name' => 'Budi Santoso',
            'condition' => 'Baik',
            'edit_url' => AssetResource::getUrl('edit', ['record' => $asset->getKey()]),
        ], $page->assetResult);
        $this->assertSame('Aset berhasil ditemukan.', $page->scanMessage);
    }

    public function test_unknown_or_invalid_scanned_codes_do_not_return_asset_details(): void
    {
        $page = new ScanAsset;
        $page->lookupAsset('PKM-AST-404');

        $this->assertNull($page->assetResult);
        $this->assertSame('Aset dengan kode PKM-AST-404 tidak ditemukan.', $page->scanMessage);

        $page->lookupAsset(str_repeat('A', 101));

        $this->assertNull($page->assetResult);
        $this->assertSame('Kode QR tidak valid. Silakan pindai kode aset yang benar.', $page->scanMessage);
    }
}
