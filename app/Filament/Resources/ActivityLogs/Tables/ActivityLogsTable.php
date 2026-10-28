<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use App\Models\ActivityLog;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->description(fn (ActivityLog $record): string => $record->created_at->diffForHumans()),

                TextColumn::make('user_name')
                    ->label('Editor / Admin')
                    ->searchable()
                    ->icon(Heroicon::OutlinedUser)
                    ->sortable(),

                TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (ActivityLog $record): string => $record->action_color)
                    ->formatStateUsing(fn (ActivityLog $record): string => $record->action_label),

                TextColumn::make('model_label')
                    ->label('Data Model')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('record_label')
                    ->label('Target Aset / Barang')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('changed_fields')
                    ->label('Kolom Diubah')
                    ->badge()
                    ->color('warning')
                    ->separator(', ')
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('loggable_type')
                    ->label('Tipe Data')
                    ->options([
                        'App\Models\Asset' => 'Aset Kantor',
                        'App\Models\Supply' => 'Perlengkapan (ATK)',
                    ]),

                SelectFilter::make('action')
                    ->label('Jenis Aksi')
                    ->options([
                        'created' => 'Data Ditambahkan (Created)',
                        'updated' => 'Data Diperbarui (Updated)',
                        'deleted' => 'Data Dihapus (Deleted)',
                    ]),
            ])
            ->recordActions([
                Action::make('view_diff')
                    ->label('Detail Perubahan')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('info')
                    ->modalHeading(fn (ActivityLog $record): string => 'Detail Log #' . $record->id . ' - ' . $record->action_label)
                    ->modalWidth(Width::Large)
                    ->modalContent(fn (ActivityLog $record) => view('filament.resources.activity-logs.detail-modal', [
                        'record' => $record,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
            ]);
    }
}
