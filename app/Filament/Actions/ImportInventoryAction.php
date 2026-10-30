<?php

namespace App\Filament\Actions;

use App\Services\InventorySpreadsheetImporter;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImportInventoryAction
{
    public static function assets(): Action
    {
        return static::make('importAssets', 'aset', 'importAssets');
    }

    public static function supplies(): Action
    {
        return static::make('importSupplies', 'perlengkapan ATK', 'importSupplies');
    }

    private static function make(string $name, string $label, string $method): Action
    {
        return Action::make($name)
            ->label('Impor '.$label.' (CSV/XLSX)')
            ->icon(Heroicon::OutlinedArrowUpTray)
            ->color('success')
            ->schema([
                FileUpload::make('file')
                    ->label('File CSV atau Excel')
                    ->acceptedFileTypes([
                        'application/csv',
                        'text/csv',
                        'text/plain',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->storeFiles(false)
                    ->required()
                    ->helperText('Gunakan baris pertama sebagai header. Kolom relasi dapat menggunakan ID atau nama kategori/ruangan yang sudah terdaftar.'),
            ])
            ->modalHeading('Impor Data '.ucfirst($label))
            ->modalSubmitActionLabel('Impor Data')
            ->action(function (array $data) use ($label, $method): void {
                /** @var TemporaryUploadedFile $file */
                $file = $data['file'];
                $path = $file->getRealPath();

                if (! is_string($path)) {
                    throw ValidationException::withMessages([
                        'file' => 'File yang diunggah tidak dapat dibaca.',
                    ]);
                }

                $importedRows = app(InventorySpreadsheetImporter::class)->{$method}(
                    $path,
                    $file->getClientOriginalExtension(),
                );

                Notification::make()
                    ->title('Impor berhasil')
                    ->body("{$importedRows} baris {$label} berhasil diproses.")
                    ->success()
                    ->send();
            });
    }
}
