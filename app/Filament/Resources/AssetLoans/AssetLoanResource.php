<?php

namespace App\Filament\Resources\AssetLoans;

use App\Filament\Resources\AssetLoans\Pages\CreateAssetLoan;
use App\Filament\Resources\AssetLoans\Pages\EditAssetLoan;
use App\Filament\Resources\AssetLoans\Pages\ListAssetLoans;
use App\Filament\Resources\AssetLoans\Schemas\AssetLoanForm;
use App\Filament\Resources\AssetLoans\Tables\AssetLoansTable;
use App\Models\AssetLoan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AssetLoanResource extends Resource
{
    protected static ?string $model = AssetLoan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnRight;

    protected static ?string $navigationLabel = 'Peminjaman Aset';

    protected static ?string $modelLabel = 'Peminjaman Aset';

    protected static ?string $pluralModelLabel = 'Peminjaman Aset';

    public static function form(Schema $schema): Schema
    {
        return AssetLoanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetLoansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetLoans::route('/'),
            'create' => CreateAssetLoan::route('/create'),
            'edit' => EditAssetLoan::route('/{record}/edit'),
        ];
    }
}
