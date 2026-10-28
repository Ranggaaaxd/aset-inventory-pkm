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
                    ->default(null),
                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->default(null),
                Select::make('condition')
                    ->options(['Baik' => 'Baik', 'Rusak Ringan' => 'Rusak ringan', 'Rusak Berat' => 'Rusak berat'])
                    ->default('Baik')
                    ->required(),
                TextInput::make('brand')
                    ->default(null),
                DatePicker::make('purchase_date'),
                TextInput::make('price')
                    ->numeric()
                    ->default(null)
                    ->prefix('$'),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
