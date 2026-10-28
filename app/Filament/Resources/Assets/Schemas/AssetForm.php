<?php

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('asset_code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null),
                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null),
                TextInput::make('pic_name')
                    ->label('Nama PIC / Pemegang Barang')
                    ->placeholder('Contoh: Budi Santoso (kosongkan jika barang bersama)')
                    ->helperText('Opsional jika barang bersama / fasilitas umum seperti AC, proyektor, atau meja meeting.')
                    ->maxLength(255),
                TextInput::make('pic_contact')
                    ->label('Kontak PIC (No. WhatsApp)')
                    ->placeholder('Contoh: 08123456789')
                    ->helperText('Nomor kontak PIC yang dapat dihubungi.')
                    ->tel()
                    ->maxLength(50),
                Select::make('condition')
                    ->options(['Baik' => 'Baik', 'Rusak Ringan' => 'Rusak Ringan', 'Rusak Berat' => 'Rusak Berat'])
                    ->default('Baik')
                    ->required(),
                TextInput::make('brand')
                    ->default(null),
                DatePicker::make('purchase_date'),
                TextInput::make('price')
                    ->numeric()
                    ->default(null)
                    ->prefix('Rp'),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
