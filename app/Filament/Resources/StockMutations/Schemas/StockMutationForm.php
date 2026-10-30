<?php

namespace App\Filament\Resources\StockMutations\Schemas;

use App\Models\Room;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class StockMutationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('supply_id')
                    ->relationship('supply', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('type')
                    ->options([
                        'in' => 'In (Barang Masuk)',
                        'out' => 'Out (Barang Keluar)',
                    ])
                    ->default('in')
                    ->live()
                    ->afterStateUpdated(function (?string $state, Set $set): void {
                        $set('room_id', $state === 'in' ? static::getStorageRoomId() : null);
                    })
                    ->required(),
                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->searchable()
                    ->preload()
                    ->label(fn (Get $get): string => $get('type') === 'in' ? 'Gudang Penyimpanan' : 'Ruangan Tujuan / Peminta')
                    ->default(fn (Get $get): ?int => $get('type') === 'in' ? static::getStorageRoomId() : null)
                    ->required(fn (Get $get): bool => $get('type') === 'out'),
                TextInput::make('recipient_name')
                    ->label(fn (Get $get): string => $get('type') === 'out' ? 'Nama Staf Pengambil / Penerima' : 'Supplier / Toko Pembelian')
                    ->placeholder(fn (Get $get): string => $get('type') === 'out' ? 'Contoh: Siti Rahma' : 'Contoh: CV ATK Jaya / Tokopedia')
                    ->required(fn (Get $get): bool => $get('type') === 'out'),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                DatePicker::make('mutation_date')
                    ->default(now())
                    ->required(),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }

    private static function getStorageRoomId(): ?int
    {
        return Room::query()
            ->where('code', 'RM-GUD')
            ->orWhere('name', 'like', '%Gudang%ATK%')
            ->value('id');
    }
}
