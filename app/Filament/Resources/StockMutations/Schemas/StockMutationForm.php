<?php

namespace App\Filament\Resources\StockMutations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null),
                Select::make('type')
                    ->options([
                        'in' => 'In (Barang Masuk)',
                        'out' => 'Out (Barang Keluar)',
                    ])
                    ->default('in')
                    ->required(),
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
}
