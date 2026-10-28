<?php

namespace App\Filament\Resources\StockMutations;

use App\Filament\Resources\StockMutations\Pages\CreateStockMutation;
use App\Filament\Resources\StockMutations\Pages\EditStockMutation;
use App\Filament\Resources\StockMutations\Pages\ListStockMutations;
use App\Filament\Resources\StockMutations\Schemas\StockMutationForm;
use App\Filament\Resources\StockMutations\Tables\StockMutationsTable;
use App\Models\StockMutation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockMutationResource extends Resource
{
    protected static ?string $model = StockMutation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return StockMutationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockMutationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockMutations::route('/'),
            'create' => CreateStockMutation::route('/create'),
            'edit' => EditStockMutation::route('/{record}/edit'),
        ];
    }
}
