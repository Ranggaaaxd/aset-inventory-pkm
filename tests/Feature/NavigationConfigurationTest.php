<?php

namespace Tests\Feature;

use App\Filament\Pages\ScanAsset;
use App\Filament\Resources\AssetLoans\AssetLoanResource;
use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Rooms\RoomResource;
use App\Filament\Resources\StockMutations\StockMutationResource;
use App\Filament\Resources\Supplies\SupplyResource;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Supply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_navigation_groups_labels_and_order_are_configured(): void
    {
        $this->assertSame('Daftar Aset Kantor', AssetResource::getNavigationLabel());
        $this->assertSame('Manajemen Aset', AssetResource::getNavigationGroup());
        $this->assertSame('Manajemen Aset', AssetLoanResource::getNavigationGroup());
        $this->assertSame('Manajemen Aset', ScanAsset::getNavigationGroup());
        $this->assertSame([1, 2, 3], [
            AssetResource::getNavigationSort(),
            AssetLoanResource::getNavigationSort(),
            ScanAsset::getNavigationSort(),
        ]);

        $this->assertSame('Perlengkapan & ATK', SupplyResource::getNavigationLabel());
        $this->assertSame('Logistik & ATK', SupplyResource::getNavigationGroup());
        $this->assertSame('Mutasi Keluar-Masuk', StockMutationResource::getNavigationLabel());
        $this->assertSame([1, 2], [
            SupplyResource::getNavigationSort(),
            StockMutationResource::getNavigationSort(),
        ]);

        $this->assertSame('Ruangan & Lokasi', RoomResource::getNavigationLabel());
        $this->assertSame('Kategori Barang', CategoryResource::getNavigationLabel());
        $this->assertSame('Riwayat Perubahan', \App\Filament\Resources\ActivityLogs\ActivityLogResource::getNavigationLabel());
        $this->assertSame([1, 2, 3], [
            RoomResource::getNavigationSort(),
            CategoryResource::getNavigationSort(),
            \App\Filament\Resources\ActivityLogs\ActivityLogResource::getNavigationSort(),
        ]);
    }

    public function test_asset_loan_badge_counts_borrowed_assets_and_uses_warning_color(): void
    {
        $asset = Asset::create([
            'asset_code' => 'PKM-AST-001',
            'name' => 'Laptop',
            'condition' => 'Baik',
        ]);

        AssetLoan::create([
            'asset_id' => $asset->id,
            'borrower_name' => 'Budi Santoso',
            'loan_date' => today(),
            'expected_return_date' => today()->addWeek(),
            'status' => 'dipinjam',
        ]);
        AssetLoan::create([
            'asset_id' => $asset->id,
            'borrower_name' => 'Siti Aminah',
            'loan_date' => today()->subWeeks(2),
            'expected_return_date' => today()->subWeek(),
            'actual_return_date' => today()->subWeek(),
            'status' => 'dikembalikan',
        ]);

        $this->assertSame('1', AssetLoanResource::getNavigationBadge());
        $this->assertSame('warning', AssetLoanResource::getNavigationBadgeColor());
    }

    public function test_supply_badge_counts_low_stock_only_and_uses_danger_color(): void
    {
        Supply::create([
            'item_code' => 'ATK-LOW',
            'name' => 'Kertas',
            'stock' => 2,
            'min_stock' => 5,
        ]);
        Supply::create([
            'item_code' => 'ATK-OK',
            'name' => 'Pulpen',
            'stock' => 10,
            'min_stock' => 3,
        ]);

        $this->assertSame('1', SupplyResource::getNavigationBadge());
        $this->assertSame('danger', SupplyResource::getNavigationBadgeColor());
    }
}
