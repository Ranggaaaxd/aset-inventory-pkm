<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Room;
use App\Models\Supply;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use OpenSpout\Reader\CSV\Options as CsvOptions;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InventorySpreadsheetImporter
{
    private const ASSET_HEADERS = [
        'asset_code' => ['asset_code', 'kode_aset'],
        'name' => ['name', 'nama_barang_aset', 'nama_barang', 'nama_aset'],
        'size' => ['size', 'ukuran'],
        'quantity' => ['quantity', 'jumlah'],
        'status' => ['status', 'status_penempatan'],
        'category_id' => ['category_id', 'kategori_id'],
        'category_name' => ['category_name', 'category', 'kategori'],
        'room_id' => ['room_id', 'ruangan_id'],
        'room_name' => ['room_name', 'room', 'ruangan'],
        'pic_name' => ['pic_name', 'pic_pemegang'],
        'pic_contact' => ['pic_contact', 'kontak_pic'],
        'condition' => ['condition', 'kondisi'],
        'brand' => ['brand', 'merk_brand', 'merk'],
        'purchase_date' => ['purchase_date', 'tgl_pembelian'],
        'price' => ['price', 'nilai_perolehan_rp'],
        'tax_group' => ['tax_group', 'golongan_pajak'],
        'useful_life_years' => ['useful_life_years', 'masa_manfaat_tahun'],
        'salvage_value' => ['salvage_value', 'nilai_residu_rp'],
        'warranty_expiry_date' => ['warranty_expiry_date', 'tanggal_habis_garansi'],
        'next_maintenance_date' => ['next_maintenance_date', 'jadwal_servis_berikutnya'],
        'notes' => ['notes', 'catatan'],
    ];

    private const SUPPLY_HEADERS = [
        'item_code' => ['item_code', 'kode_barang'],
        'name' => ['name', 'nama_perlengkapan_atk', 'nama_barang'],
        'category_id' => ['category_id', 'kategori_id'],
        'category_name' => ['category_name', 'category', 'kategori'],
        'stock' => ['stock', 'stok'],
        'min_stock' => ['min_stock', 'stok_minimum'],
        'unit' => ['unit', 'satuan'],
        'notes' => ['notes', 'catatan'],
    ];

    public function importAssets(string $path, string $extension): int
    {
        return $this->import($path, $extension, Asset::class, self::ASSET_HEADERS, 'asset');
    }

    public function importSupplies(string $path, string $extension): int
    {
        return $this->import($path, $extension, Supply::class, self::SUPPLY_HEADERS, 'supply');
    }

    /**
     * @param  class-string<Asset|Supply>  $modelClass
     * @param  array<string, array<string>>  $headers
     */
    private function import(string $path, string $extension, string $modelClass, array $headers, string $type): int
    {
        if (strtolower($extension) === 'xlsx') {
            $reader = IOFactory::createReaderForFile($path);
            $reader->setReadDataOnly(false);
            $spreadsheet = $reader->load($path);

            try {
                return $this->importRows(
                    $spreadsheet->getActiveSheet()->toArray(null, true, true, false),
                    $modelClass,
                    $headers,
                    $type,
                );
            } finally {
                $spreadsheet->disconnectWorksheets();
            }
        }

        if (strtolower($extension) !== 'csv') {
            throw ValidationException::withMessages([
                'file' => 'Format file tidak didukung. Gunakan CSV atau XLSX.',
            ]);
        }

        $reader = new CsvReader($this->csvOptions($path));
        $reader->open($path);

        try {
            $rows = (function () use ($reader): \Generator {
                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {
                        yield $row->toArray();
                    }

                    break;
                }
            })();

            return $this->importRows($rows, $modelClass, $headers, $type);
        } finally {
            $reader->close();
        }
    }

    /**
     * @param  iterable<array<mixed>>  $rows
     * @param  class-string<Asset|Supply>  $modelClass
     * @param  array<string, array<string>>  $headers
     */
    private function importRows(iterable $rows, string $modelClass, array $headers, string $type): int
    {
        return DB::transaction(function () use ($rows, $modelClass, $headers, $type): int {
            $imported = 0;
            $hasHeader = false;
            $rowNumber = 0;

            foreach ($rows as $values) {
                $rowNumber++;

                if (! $hasHeader) {
                    $headersByColumn = $this->mapHeaders($values, $headers);
                    $hasHeader = true;

                    if (! in_array('name', $headersByColumn, true)) {
                        throw ValidationException::withMessages([
                            'file' => 'File impor harus memiliki kolom nama barang (name atau Nama Barang).',
                        ]);
                    }

                    continue;
                }

                $data = [];
                foreach ($headersByColumn as $index => $attribute) {
                    if ($attribute === null || ! array_key_exists($index, $values)) {
                        continue;
                    }

                    $value = $this->normalizeValue($values[$index], $rowNumber);
                    if ($value !== null) {
                        $data[$attribute] = $value;
                    }
                }

                if (empty(array_filter($data, fn (mixed $value): bool => $value !== null && $value !== ''))) {
                    continue;
                }

                $this->importRow($modelClass, $data, $type, $rowNumber);
                $imported++;
            }

            if (! $hasHeader) {
                throw ValidationException::withMessages([
                    'file' => 'File impor kosong atau tidak memiliki baris header.',
                ]);
            }

            return $imported;
        });
    }

    private function csvOptions(string $path): CsvOptions
    {
        $stream = fopen($path, 'rb');

        if ($stream === false) {
            throw ValidationException::withMessages([
                'file' => 'File CSV tidak dapat dibaca.',
            ]);
        }

        $header = fgets($stream);
        fclose($stream);

        $header = ltrim((string) $header, "\xEF\xBB\xBF");
        $options = new CsvOptions;
        $options->FIELD_DELIMITER = substr_count($header, ';') > substr_count($header, ',') ? ';' : ',';

        return $options;
    }

    /**
     * @param  array<mixed>  $values
     * @param  array<string, array<string>>  $headers
     * @return array<int, string|null>
     */
    private function mapHeaders(array $values, array $headers): array
    {
        $headerMap = [];
        foreach ($headers as $attribute => $aliases) {
            foreach ($aliases as $alias) {
                $headerMap[$this->normalizeHeader($alias)] = $attribute;
            }
        }

        return array_map(
            fn (mixed $header): ?string => $headerMap[$this->normalizeHeader((string) $header)] ?? null,
            $values,
        );
    }

    private function normalizeHeader(string $header): string
    {
        return Str::slug(mb_strtolower(trim($header, " \t\n\r\0\x0B\xEF\xBB\xBF"), 'UTF-8'), '_');
    }

    private function normalizeValue(mixed $value, int $rowNumber): mixed
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (! is_string($value)) {
            return $value;
        }

        $value = trim($value);

        if ($value === '' || $value === '-') {
            return null;
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            $date = Carbon::createFromFormat('!d/m/Y', $value);
            $errors = Carbon::getLastErrors();

            if (! $date || ($errors && ($errors['warning_count'] || $errors['error_count']))) {
                $this->throwRowError($rowNumber, "Tanggal [{$value}] tidak valid.");
            }

            return $date->toDateString();
        }

        return $value;
    }

    private function normalizeNumber(string $value): string
    {
        $number = preg_replace('/[^\d,.-]/u', '', $value) ?? '';

        if (str_contains($number, ',') && str_contains($number, '.')) {
            return str_replace(',', '.', str_replace('.', '', $number));
        }

        if (str_contains($number, ',')) {
            if (preg_match('/^\d{1,3}(?:,\d{3})+$/', $number)) {
                return str_replace(',', '', $number);
            }

            return str_replace(',', '.', $number);
        }

        if (preg_match('/^\d{1,3}(?:\.\d{3})+$/', $number)) {
            return str_replace('.', '', $number);
        }

        return $number;
    }

    /**
     * @param  class-string<Asset|Supply>  $modelClass
     * @param  array<string, mixed>  $data
     */
    private function importRow(string $modelClass, array $data, string $type, int $rowNumber): void
    {
        if ($type === 'asset') {
            $taxGroup = mb_strtolower(trim((string) ($data['tax_group'] ?? '')));
            $data['useful_life_years'] = match (true) {
                str_contains($taxGroup, 'golongan 1') => 4,
                str_contains($taxGroup, 'golongan 2') => 8,
                default => 0,
            };
        }

        if (array_key_exists('category_name', $data)) {
            $categoryName = trim((string) $data['category_name']);
            $categoryQuery = Category::query()
                ->whereIn('type', [$type, 'all'])
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($categoryName)]);
            $category = $categoryQuery->first()
                ?? Category::query()
                    ->whereIn('type', [$type, 'all'])
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($categoryName).'%'])
                    ->first();
            if (! $category) {
                $this->throwRowError($rowNumber, "Kategori [{$data['category_name']}] tidak ditemukan.");
            }
            $data['category_id'] = $category->getKey();
            unset($data['category_name']);
        }

        if (array_key_exists('room_name', $data)) {
            $roomName = trim((string) $data['room_name']);
            $room = Room::query()
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($roomName)])
                ->first()
                ?? Room::query()
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($roomName).'%'])
                    ->first();
            if (! $room) {
                $this->throwRowError($rowNumber, "Ruangan [{$data['room_name']}] tidak ditemukan.");
            }
            $data['room_id'] = $room->getKey();
            unset($data['room_name']);
        }

        foreach (['price', 'salvage_value', 'quantity', 'useful_life_years', 'stock', 'min_stock'] as $numericField) {
            if (isset($data[$numericField]) && is_string($data[$numericField])) {
                $data[$numericField] = $this->normalizeNumber($data[$numericField]);
            }
        }

        $identifier = $type === 'asset' ? 'asset_code' : 'item_code';
        if ($type === 'asset' && blank($data[$identifier] ?? null)) {
            do {
                $data[$identifier] = Asset::generateNextAssetCode();
            } while (Asset::query()->where($identifier, $data[$identifier])->exists());
        }
        $identifierValue = $data[$identifier] ?? null;

        $rules = $type === 'asset'
            ? [
                'name' => ['required', 'string', 'max:255'],
                'quantity' => ['nullable', 'integer', 'min:1'],
                'category_id' => ['nullable', 'integer', 'exists:categories,id'],
                'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
                'condition' => ['nullable', 'in:Baik,Rusak Ringan,Rusak Berat'],
                'purchase_date' => ['nullable', 'date'],
                'warranty_expiry_date' => ['nullable', 'date'],
                'next_maintenance_date' => ['nullable', 'date'],
                'status' => ['nullable', 'in:'.implode(',', array_keys(Asset::STATUSES))],
                'price' => ['nullable', 'numeric', 'min:0'],
                'useful_life_years' => ['nullable', 'integer', 'min:0'],
                'salvage_value' => ['nullable', 'integer', 'min:0'],
            ]
            : [
                'name' => ['required', 'string', 'max:255'],
                'stock' => ['nullable', 'integer', 'min:0'],
                'min_stock' => ['nullable', 'integer', 'min:0'],
                'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $this->throwRowError($rowNumber, $validator->errors()->first());
        }

        $record = $identifierValue
            ? $modelClass::query()->firstOrNew([$identifier => $identifierValue])
            : new $modelClass;

        $record->fill(collect($data)->except(['category_name', 'room_name'])->all());
        $record->save();
    }

    private function throwRowError(int $rowNumber, string $message): never
    {
        throw ValidationException::withMessages([
            'file' => "Baris {$rowNumber}: {$message}",
        ]);
    }
}
