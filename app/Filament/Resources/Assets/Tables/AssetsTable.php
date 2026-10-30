<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Filament\Actions\ImportInventoryAction;
use App\Models\Asset;
use App\Services\CsvTemplateDownload;
use App\Services\ExportService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset_code')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->searchable(),
                TextColumn::make('room.name')
                    ->searchable(),
                TextColumn::make('pic_name')
                    ->label('PIC / Pemegang')
                    ->searchable()
                    ->placeholder('Barang Bersama')
                    ->description(fn (Asset $record): ?string => $record->pic_contact)
                    ->sortable(),
                TextColumn::make('condition')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baik' => 'success',
                        'Rusak Ringan' => 'warning',
                        'Rusak Berat' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('price')
                    ->label('Harga Perolehan')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status Penempatan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Tersedia di Gudang' => 'info',
                        'Digunakan' => 'success',
                        'Dipinjam' => 'warning',
                        'Rusak/Afkir' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('next_maintenance_date')
                    ->label('Jadwal Servis')
                    ->badge()
                    ->formatStateUsing(fn (?Carbon $state): string => match (true) {
                        ! $state => '-',
                        $state->lt(today()) => 'Lewat Jadwal',
                        $state->lte(today()->addDays(7)) => 'Jatuh Tempo',
                        default => $state->format('d/m/Y'),
                    })
                    ->color(fn (?Carbon $state): string => match (true) {
                        ! $state => 'gray',
                        $state->lt(today()) => 'danger',
                        $state->lte(today()->addDays(7)) => 'warning',
                        default => 'success',
                    })
                    ->sortable(),
                TextColumn::make('warranty_expiry_date')
                    ->label('Garansi Berakhir')
                    ->badge()
                    ->formatStateUsing(fn (?Carbon $state): string => ! $state
                        ? '-'
                        : ($state->lte(today()) ? 'Garansi Habis' : $state->format('d/m/Y')))
                    ->color(fn (?Carbon $state): string => ! $state
                        ? 'gray'
                        : ($state->lte(today()) ? 'danger' : 'success'))
                    ->sortable(),
                TextColumn::make('brand')
                    ->searchable(),
                TextColumn::make('purchase_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('current_book_value')
                    ->label('Nilai Buku Saat Ini')
                    ->money('IDR', locale: 'id'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('room_id')
                    ->relationship('room', 'name')
                    ->label('Ruangan')
                    ->preload(),
                SelectFilter::make('condition')
                    ->label('Kondisi')
                    ->options([
                        'Baik' => 'Baik',
                        'Rusak Ringan' => 'Rusak Ringan',
                        'Rusak Berat' => 'Rusak Berat',
                    ]),
                SelectFilter::make('status')
                    ->label('Status Penempatan')
                    ->options(Asset::STATUSES),
                SelectFilter::make('ownership')
                    ->label('Status Kepemilikan (PIC)')
                    ->options([
                        'has_pic' => 'Ada PIC (Aset Pribadi)',
                        'shared' => 'Barang Bersama / Tanpa PIC',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (($data['value'] ?? null) === 'has_pic') {
                            $query->whereNotNull('pic_name')->where('pic_name', '!=', '');
                        } elseif (($data['value'] ?? null) === 'shared') {
                            $query->where(function (Builder $q) {
                                $q->whereNull('pic_name')->orWhere('pic_name', '');
                            });
                        }
                    }),
            ])
            ->headerActions([
                ActionGroup::make([
                    Action::make('export_excel')
                        ->label('Ekspor ke Excel (.xlsx)')
                        ->icon(Heroicon::OutlinedArrowDownTray)
                        ->color('success')
                        ->action(fn () => ExportService::exportAssets(null, 'xlsx')),
                    Action::make('export_csv')
                        ->label('Ekspor ke CSV (.csv)')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->color('info')
                        ->action(fn () => ExportService::exportAssets(null, 'csv')),
                ])
                    ->label('Ekspor Excel/CSV')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->color('primary')
                    ->button(),
                Action::make('print_official_report')
                    ->label('Cetak Laporan Resmi')
                    ->icon(Heroicon::OutlinedPrinter)
                    ->color('gray')
                    ->url(route('reports.assets'), shouldOpenInNewTab: true),
                Action::make('downloadTemplate')
                    ->label('Unduh Template Excel')
                    ->icon(Heroicon::OutlinedDocumentArrowDown)
                    ->color('gray')
                    ->action(fn () => CsvTemplateDownload::download(
                        'template_aset_pkm.csv',
                        ['name', 'category_name', 'room_name', 'brand', 'condition', 'price', 'status', 'pic_name'],
                        ['Proyektor Epson', 'Elektronik', 'Ruang Rapat', 'Epson', 'Baik', '5000000', 'Digunakan', 'Siti Rahma'],
                    )),
                ImportInventoryAction::assets(),
            ])
            ->recordActions([
                Action::make('disposeAsset')
                    ->label('Pelepasan Aset')
                    ->icon(Heroicon::OutlinedArchiveBoxXMark)
                    ->color('danger')
                    ->schema([
                        DatePicker::make('disposal_date')
                            ->label('Tanggal Pelepasan')
                            ->default(today())
                            ->required(),
                        Select::make('disposal_method')
                            ->label('Metode Pelepasan')
                            ->options([
                                'Dimusnahkan' => 'Dimusnahkan',
                                'Dijual / Dilelang' => 'Dijual / Dilelang',
                                'Dihibahkan' => 'Dihibahkan',
                            ])
                            ->required()
                            ->live(),
                        TextInput::make('disposal_amount')
                            ->label('Nilai Penjualan')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefix('Rp')
                            ->required()
                            ->visible(fn ($get): bool => $get('disposal_method') === 'Dijual / Dilelang'),
                        Textarea::make('reason')
                            ->label('Alasan Teknis Penghapusan')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('approved_by')
                            ->label('Nama Pejabat Penyetuju')
                            ->placeholder('Contoh: Direktur')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->modalHeading('Pelepasan Aset')
                    ->modalSubmitActionLabel('Simpan Pelepasan')
                    ->requiresConfirmation()
                    ->visible(fn (Asset $record): bool => ! $record->disposal()->exists())
                    ->action(function (Asset $record, array $data): void {
                        DB::transaction(function () use ($record, $data): void {
                            $record->disposal()->create([
                                ...$data,
                                'disposal_amount' => $data['disposal_method'] === 'Dijual / Dilelang'
                                    ? $data['disposal_amount']
                                    : 0,
                            ]);
                            $record->update(['status' => 'Rusak/Afkir']);
                        });
                    }),
                Action::make('printBap')
                    ->label('Cetak BAP')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->color('gray')
                    ->url(fn (Asset $record): string => route('assets.disposal.bap', ['asset' => $record]))
                    ->openUrlInNewTab()
                    ->visible(fn (Asset $record): bool => $record->disposal()->exists()),
                Action::make('recordMaintenance')
                    ->label('Update Servis')
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->color('success')
                    ->schema([
                        DatePicker::make('maintenance_date')
                            ->label('Tanggal Servis')
                            ->default(today())
                            ->required(),
                        TextInput::make('vendor_name')
                            ->label('Bengkel / Teknisi')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('cost')
                            ->label('Biaya Servis')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefix('Rp')
                            ->required(),
                        Textarea::make('description')
                            ->label('Detail Perbaikan / Penggantian')
                            ->required()
                            ->columnSpanFull(),
                        DatePicker::make('next_maintenance_date')
                            ->label('Tanggal Servis Berikutnya')
                            ->default(now()->addDays(90))
                            ->required(),
                        Select::make('condition')
                            ->label('Kondisi Setelah Servis')
                            ->options([
                                'Baik' => 'Baik',
                                'Rusak Ringan' => 'Rusak Ringan',
                                'Rusak Berat' => 'Rusak Berat',
                            ])
                            ->default('Baik')
                            ->required(),
                    ])
                    ->modalHeading('Catat Servis Aset')
                    ->modalSubmitActionLabel('Simpan Update')
                    ->action(function (Asset $record, array $data): void {
                        DB::transaction(function () use ($record, $data): void {
                            $record->maintenances()->create([
                                'maintenance_date' => $data['maintenance_date'],
                                'vendor_name' => $data['vendor_name'],
                                'cost' => $data['cost'],
                                'description' => $data['description'],
                            ]);

                            $record->update([
                                'condition' => $data['condition'],
                                'next_maintenance_date' => $data['next_maintenance_date'],
                            ]);
                        });
                    }),
                ViewAction::make('quickView')
                    ->label('Lihat Detail')
                    ->icon(Heroicon::OutlinedEye)
                    ->modalWidth(Width::TwoExtraLarge)
                    ->infolist([
                        Section::make('Informasi Aset')
                            ->columns(3)
                            ->columnSpanFull()
                            ->schema([
                                ImageEntry::make('image')
                                    ->label('Foto Aset')
                                    ->disk('public')
                                    ->imageHeight(160)
                                    ->columnSpan(1),
                                Group::make([
                                    TextEntry::make('name')
                                        ->label('Nama Barang')
                                        ->size('lg')
                                        ->weight('bold'),
                                    TextEntry::make('asset_code')
                                        ->label('Kode Aset')
                                        ->badge()
                                        ->color('gray'),
                                ])
                                    ->columnSpan(2),
                            ]),
                        Section::make('Penempatan')
                            ->columns(2)
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('room.name')
                                    ->label('Ruangan')
                                    ->placeholder('-'),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Tersedia di Gudang' => 'info',
                                        'Digunakan' => 'success',
                                        'Dipinjam' => 'warning',
                                        'Rusak/Afkir' => 'danger',
                                        default => 'gray',
                                    }),
                                TextEntry::make('pic_name')
                                    ->label('Nama PIC')
                                    ->placeholder('Barang Bersama'),
                                TextEntry::make('pic_contact')
                                    ->label('Kontak WhatsApp')
                                    ->placeholder('-'),
                            ]),
                        Section::make('Akuntansi')
                            ->columns(3)
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('price')
                                    ->label('Harga Perolehan')
                                    ->money('IDR', locale: 'id')
                                    ->placeholder('-'),
                                TextEntry::make('current_book_value')
                                    ->label('Nilai Buku Saat Ini')
                                    ->money('IDR', locale: 'id'),
                                TextEntry::make('tax_group')
                                    ->label('Golongan Pajak')
                                    ->placeholder('-'),
                            ]),
                        Section::make('Pemeliharaan')
                            ->columns(2)
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('condition')
                                    ->label('Kondisi')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Baik' => 'success',
                                        'Rusak Ringan' => 'warning',
                                        'Rusak Berat' => 'danger',
                                        default => 'gray',
                                    }),
                                TextEntry::make('warranty_expiry_date')
                                    ->label('Tanggal Habis Garansi')
                                    ->date('d/m/Y')
                                    ->placeholder('-'),
                                TextEntry::make('next_maintenance_date')
                                    ->label('Jadwal Servis Berikutnya')
                                    ->date('d/m/Y')
                                    ->placeholder('-'),
                                TextEntry::make('notes')
                                    ->label('Catatan')
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ]),
                        Section::make('Riwayat Servis')
                            ->columns(2)
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('maintenance_total_cost')
                                    ->label('Total Biaya Servis')
                                    ->state(fn (Asset $record): int => (int) $record->maintenances()->sum('cost'))
                                    ->money('IDR', locale: 'id')
                                    ->columnSpanFull(),
                                RepeatableEntry::make('maintenances')
                                    ->label('Catatan Servis')
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('maintenance_date')
                                            ->label('Tanggal')
                                            ->date('d/m/Y'),
                                        TextEntry::make('vendor_name')
                                            ->label('Bengkel / Teknisi'),
                                        TextEntry::make('cost')
                                            ->label('Biaya')
                                            ->money('IDR', locale: 'id'),
                                        TextEntry::make('description')
                                            ->label('Detail Perbaikan')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->extraModalFooterActions([
                        Action::make('printLabel')
                            ->label('Cetak Label')
                            ->icon(Heroicon::OutlinedPrinter)
                            ->color('info')
                            ->modalHeading(fn (Asset $record): string => 'Cetak Stiker Label - '.$record->asset_code)
                            ->modalWidth(Width::Medium)
                            ->modalContent(fn (Asset $record) => view('filament.resources.assets.print-label-modal', [
                                'record' => $record,
                            ]))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup'),
                    ]),
                Action::make('print_label')
                    ->label('Cetak Label')
                    ->icon(Heroicon::OutlinedPrinter)
                    ->color('info')
                    ->modalHeading(fn (Asset $record): string => 'Cetak Stiker Label - '.$record->asset_code)
                    ->modalWidth(Width::Medium)
                    ->modalContent(fn (Asset $record) => view('filament.resources.assets.print-label-modal', [
                        'record' => $record,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
                Action::make('print_bast')
                    ->label('Cetak BAST')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->color('primary')
                    ->url(fn (Asset $record): string => route('assets.bast', ['asset' => $record]))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('export_selected_excel')
                        ->label('Ekspor Terpilih ke Excel (.xlsx)')
                        ->icon(Heroicon::OutlinedArrowDownTray)
                        ->action(fn (Collection $records) => ExportService::exportAssets($records, 'xlsx')),
                    BulkAction::make('export_selected_csv')
                        ->label('Ekspor Terpilih ke CSV (.csv)')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->action(fn (Collection $records) => ExportService::exportAssets($records, 'csv')),
                    BulkAction::make('print_selected_labels')
                        ->label('Cetak Label Terpilih')
                        ->icon(Heroicon::OutlinedPrinter)
                        ->color('info')
                        ->action(function (Collection $records) {
                            $ids = $records->pluck('id')->join(',');

                            return redirect()->route('assets.print-batch', ['ids' => $ids]);
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
