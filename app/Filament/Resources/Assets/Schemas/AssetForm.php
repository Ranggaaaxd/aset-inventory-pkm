<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Models\Asset;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class AssetForm
{
    public static function configure(Schema $schema, bool $lockRoomToOwner = false): Schema
    {
        $roomField = Select::make('room_id')
            ->relationship('room', 'name')
            ->searchable()
            ->preload()
            ->default(null);

        if ($lockRoomToOwner) {
            $roomField
                ->default(fn ($livewire) => $livewire->getOwnerRecord()->id)
                ->disabled()
                ->dehydrated()
                ->helperText('Otomatis terkunci sesuai ruangan yang sedang Anda buka saat ini.');
        }

        return $schema
            ->components([
                Wizard::make([
                    Step::make('Informasi Utama')
                        ->icon('heroicon-o-cube')
                        ->columns(2)
                        ->schema([
                            TextInput::make('asset_code')
                                ->default(fn (): string => Asset::generateNextAssetCode())
                                ->disabled()
                                ->required(),
                            TextInput::make('name')
                                ->required(),
                            Select::make('category_id')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->default(null)
                                ->required(),
                            $roomField,
                            TextInput::make('brand')
                                ->default(null),
                            Select::make('condition')
                                ->options(['Baik' => 'Baik', 'Rusak Ringan' => 'Rusak Ringan', 'Rusak Berat' => 'Rusak Berat'])
                                ->default('Baik')
                                ->required(),
                        ]),
                    Step::make('Status & PIC')
                        ->icon('heroicon-o-user-circle')
                        ->columns(2)
                        ->schema([
                            Select::make('status')
                                ->options(Asset::STATUSES)
                                ->disableOptionWhen(fn (string $value): bool => $value === 'Dipinjam')
                                ->default('Tersedia di Gudang')
                                ->required(),
                            TextInput::make('pic_name')
                                ->label('Nama PIC / Pemegang Barang')
                                ->placeholder('Contoh: Budi Santoso')
                                ->helperText('Kosongkan jika barang masih berupa stok cadangan di gudang')
                                ->maxLength(255),
                            TextInput::make('pic_contact')
                                ->label('Kontak PIC (No. WhatsApp)')
                                ->placeholder('Contoh: 08123456789')
                                ->helperText('Nomor kontak PIC yang dapat dihubungi.')
                                ->tel()
                                ->maxLength(50),
                        ]),
                    Step::make('Akuntansi & Pajak')
                        ->icon('heroicon-o-banknotes')
                        ->columns(2)
                        ->schema([
                            TextInput::make('price')
                                ->numeric()
                                ->default(null)
                                ->prefix('Rp'),
                            DatePicker::make('purchase_date'),
                            Select::make('tax_group')
                                ->label('Golongan Fiskal Pajak')
                                ->options([
                                    'Golongan 1' => 'Golongan 1 (4 Tahun - Komputer/IT)',
                                    'Golongan 2' => 'Golongan 2 (8 Tahun - Mebel/AC/Kendaraan)',
                                    'Non-Depresiasi' => 'Non-Depresiasi',
                                ])
                                ->live()
                                ->afterStateUpdated(function (?string $state, Set $set): void {
                                    $set('useful_life_years', match ($state) {
                                        'Golongan 1' => 4,
                                        'Golongan 2' => 8,
                                        'Non-Depresiasi' => 0,
                                        default => null,
                                    });
                                })
                                ->default(null),
                            TextInput::make('useful_life_years')
                                ->label('Masa Manfaat (Tahun)')
                                ->numeric()
                                ->integer()
                                ->minValue(fn (Get $get): int => $get('tax_group') === 'Non-Depresiasi' ? 0 : 1)
                                ->default(4)
                                ->required(fn (Get $get): bool => $get('tax_group') !== 'Non-Depresiasi'),
                            TextInput::make('salvage_value')
                                ->label('Nilai Residu / Sisa (Rp)')
                                ->numeric()
                                ->integer()
                                ->minValue(0)
                                ->default(0)
                                ->prefix('Rp'),
                        ]),
                    Step::make('Foto & Pemeliharaan')
                        ->icon('heroicon-o-photo')
                        ->columns(2)
                        ->schema([
                            FileUpload::make('image')
                                ->label('Foto Aset')
                                ->image()
                                ->disk('public')
                                ->directory('assets')
                                ->visibility('public')
                                ->maxSize(5120)
                                ->columnSpanFull(),
                            DatePicker::make('warranty_expiry_date')
                                ->label('Tanggal Habis Garansi'),
                            DatePicker::make('next_maintenance_date')
                                ->label('Jadwal Servis Berikutnya'),
                            Textarea::make('notes')
                                ->default(null)
                                ->columnSpanFull(),
                        ]),
                ])
                    ->columnSpanFull()
                    ->submitAction(new HtmlString(Blade::render(
                        '<x-filament::button type="submit">Simpan</x-filament::button>',
                    ))),
            ]);
    }
}
