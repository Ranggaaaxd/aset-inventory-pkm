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
                    ->required(),
                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->default(null),
                Select::make('type')
                    ->options(['in' => 'In', 'out' => 'Out'])
                    ->default('in')
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                TextInput::make('stock_before')
                    ->required()
                    ->numeric(),
                TextInput::make('stock_after')
                    ->required()
                    ->numeric(),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                DatePicker::make('mutation_date')
                    ->required(),
            ]);
    }
}
