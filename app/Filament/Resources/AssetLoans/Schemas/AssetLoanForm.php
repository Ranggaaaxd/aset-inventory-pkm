<?php

namespace App\Filament\Resources\AssetLoans\Schemas;

use App\Models\Asset;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class AssetLoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('asset_id')
                    ->label('Aset')
                    ->relationship(
                        'asset',
                        'name',
                        modifyQueryUsing: fn (Builder $query) => $query->loanable(),
                    )
                    ->getOptionLabelFromRecordUsing(fn (Asset $record): string => "{$record->asset_code} - {$record->name}")
                    ->searchable(['asset_code', 'name'])
                    ->preload()
                    ->required(),
                TextInput::make('borrower_name')
                    ->label('Nama Peminjam')
                    ->required()
                    ->maxLength(255),
                TextInput::make('borrower_department')
                    ->label('Divisi')
                    ->maxLength(255),
                TextInput::make('borrower_contact')
                    ->label('Kontak Peminjam')
                    ->tel()
                    ->maxLength(255),
                DatePicker::make('loan_date')
                    ->label('Tanggal Pinjam')
                    ->default(now())
                    ->required(),
                DatePicker::make('expected_return_date')
                    ->label('Target Pengembalian')
                    ->required()
                    ->afterOrEqual('loan_date'),
                Textarea::make('purpose')
                    ->label('Keperluan Peminjaman')
                    ->columnSpanFull(),
            ]);
    }
}
