<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ScanAsset extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static ?string $navigationLabel = 'Scan QR Aset';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Aset';

    protected static ?string $title = 'Scan QR Aset';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.scan-asset';

    public ?array $assetResult = null;

    public ?string $scanMessage = null;

    public function lookupAsset(string $assetCode): void
    {
        $assetCode = trim($assetCode);

        if ($assetCode === '' || strlen($assetCode) > 100) {
            $this->assetResult = null;
            $this->scanMessage = 'Kode QR tidak valid. Silakan pindai kode aset yang benar.';

            return;
        }

        $asset = Asset::query()
            ->with('room:id,name')
            ->where('asset_code', $assetCode)
            ->first();

        if (! $asset) {
            $this->assetResult = null;
            $this->scanMessage = "Aset dengan kode {$assetCode} tidak ditemukan.";

            return;
        }

        $this->assetResult = [
            'asset_code' => $asset->asset_code,
            'name' => $asset->name,
            'room' => $asset->room?->name ?? '-',
            'pic_name' => $asset->pic_name ?: 'Barang Bersama',
            'condition' => $asset->condition,
            'edit_url' => AssetResource::getUrl('edit', ['record' => $asset->getKey()]),
        ];
        $this->scanMessage = 'Aset berhasil ditemukan.';
    }
}
