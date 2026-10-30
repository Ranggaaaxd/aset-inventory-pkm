<?php

namespace App\Filament\Resources\AssetLoans\Tables;

use App\Models\AssetLoan;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AssetLoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Semua Fasilitas Kantor Aman')
            ->emptyStateDescription('Saat ini tidak ada aset atau peralatan kantor yang sedang dipinjam oleh staf.')
            ->emptyStateIcon('heroicon-o-shield-check')
            ->columns([
                TextColumn::make('borrower_name')
                    ->label('Nama Peminjam')
                    ->searchable()
                    ->description(fn (AssetLoan $record): ?string => $record->borrower_department),
                TextColumn::make('asset.name')
                    ->label('Aset')
                    ->searchable()
                    ->description(fn (AssetLoan $record): ?string => $record->asset?->asset_code),
                TextColumn::make('loan_date')
                    ->label('Tanggal Pinjam')
                    ->date()
                    ->sortable(),
                TextColumn::make('expected_return_date')
                    ->label('Target Kembali')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dipinjam' => 'warning',
                        'dikembalikan' => 'success',
                        'terlambat' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->recordActions([
                Action::make('return')
                    ->label('Kembalikan Barang')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->schema([
                        Textarea::make('return_notes')
                            ->label('Catatan Kondisi Barang Saat Kembali'),
                    ])
                    ->modalHeading('Konfirmasi Pengembalian Barang')
                    ->modalSubmitActionLabel('Konfirmasi Pengembalian')
                    ->visible(fn (AssetLoan $record): bool => in_array($record->status, ['dipinjam', 'terlambat'], true))
                    ->action(function (AssetLoan $record, array $data): void {
                        $record->update([
                            'status' => 'dikembalikan',
                            'actual_return_date' => today(),
                            'return_notes' => $data['return_notes'] ?? null,
                        ]);
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
