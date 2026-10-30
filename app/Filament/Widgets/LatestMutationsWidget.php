<?php

namespace App\Filament\Widgets;

use App\Models\StockMutation;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestMutationsWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected function getTableHeading(): ?string
    {
        return 'Riwayat Pengambilan ATK Terkini';
    }

    protected function getTableQuery(): Builder
    {
        return StockMutation::query()
            ->with(['supply', 'room'])
            ->latest()
            ->orderByDesc('id')
            ->limit(5);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mutation_date')
                    ->label('Tanggal')
                    ->date('d M'),
                TextColumn::make('supply.name')
                    ->label('Nama Barang')
                    ->description(fn (StockMutation $record): string => $record->quantity.' '.($record->supply?->unit ?? '')),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'in' => 'success',
                        'out' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match (strtolower($state)) {
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        default => strtoupper($state),
                    }),
                TextColumn::make('recipient_name')
                    ->label('Penerima / Supplier')
                    ->placeholder('-'),
            ])
            ->paginated(false);
    }
}
