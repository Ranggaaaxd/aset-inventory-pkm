<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Room;
use App\Models\Supply;
use App\Services\InventorySpreadsheetImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Tests\TestCase;

class InventorySpreadsheetImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_csv_assets_and_resolves_category_and_room_names(): void
    {
        Category::create(['name' => 'Elektronik', 'type' => 'asset']);
        $room = Room::create(['code' => 'RR', 'name' => 'Ruang Rapat']);
        $path = $this->createCsv(
            "asset_code,name,kategori,ruangan,kondisi,status,price\nPKM-AST-2026-0999,Proyektor,Elektronik,Ruang Rapat,Baik,Digunakan,\"1.250.000\"\n",
        );

        try {
            $imported = app(InventorySpreadsheetImporter::class)->importAssets($path, 'csv');
        } finally {
            @unlink($path);
        }

        $asset = Asset::query()->where('asset_code', 'PKM-AST-2026-0999')->firstOrFail();

        $this->assertSame(1, $imported);
        $this->assertSame('Proyektor', $asset->name);
        $this->assertSame($room->id, $asset->room_id);
        $this->assertSame('1250000.00', $asset->price);
    }

    public function test_it_imports_assets_from_the_semicolon_delimited_download_template(): void
    {
        Category::create(['name' => 'Elektronik', 'type' => 'asset']);
        Room::create(['code' => 'RR', 'name' => 'Ruang Rapat']);
        $path = $this->createCsv(
            "\xEF\xBB\xBFname;category_name;room_name;brand;condition;price;status;pic_name\n".
            "Proyektor Epson;Elektronik;Ruang Rapat;Epson;Baik;5000000;Digunakan;Siti Rahma\n",
        );

        try {
            $imported = app(InventorySpreadsheetImporter::class)->importAssets($path, 'csv');
        } finally {
            @unlink($path);
        }

        $this->assertSame(1, $imported);
        $this->assertDatabaseHas('assets', [
            'name' => 'Proyektor Epson',
            'brand' => 'Epson',
            'condition' => 'Baik',
            'status' => 'Digunakan',
            'pic_name' => 'Siti Rahma',
        ]);
    }

    public function test_it_imports_supplies_and_generates_codes_when_missing(): void
    {
        Category::create(['name' => 'ATK', 'type' => 'supply']);
        $path = $this->createCsv(
            "name,kategori,stock,min_stock,unit\nKertas A4,ATK,20,5,ream\n",
        );

        try {
            $imported = app(InventorySpreadsheetImporter::class)->importSupplies($path, 'csv');
        } finally {
            @unlink($path);
        }

        $supply = Supply::query()->where('name', 'Kertas A4')->firstOrFail();

        $this->assertSame(1, $imported);
        $this->assertSame('PKM-ATK-0001', $supply->item_code);
        $this->assertSame(20, $supply->stock);
        $this->assertSame('ATK', $supply->category->name);
    }

    public function test_it_imports_xlsx_files(): void
    {
        $temporaryPath = tempnam(sys_get_temp_dir(), 'inventory-import-');
        $path = $temporaryPath.'.xlsx';
        unlink($temporaryPath);
        $writer = new Writer;
        $writer->openToFile($path);
        $writer->addRow(Row::fromValues(['asset_code', 'name', 'condition']));
        $writer->addRow(Row::fromValues(['PKM-AST-2026-0998', 'Printer', 'Baik']));
        $writer->close();

        try {
            $imported = app(InventorySpreadsheetImporter::class)->importAssets($path, 'xlsx');
        } finally {
            @unlink($path);
        }

        $this->assertSame(1, $imported);
        $this->assertDatabaseHas('assets', [
            'asset_code' => 'PKM-AST-2026-0998',
            'name' => 'Printer',
            'condition' => 'Baik',
        ]);
    }

    public function test_invalid_csv_rows_report_the_row_and_roll_back_the_import(): void
    {
        $path = $this->createCsv(
            "asset_code,name\nPKM-AST-2026-1000,Valid\nPKM-AST-2026-1001,\n",
        );

        try {
            try {
                app(InventorySpreadsheetImporter::class)->importAssets($path, 'csv');
                $this->fail('Invalid rows should fail validation.');
            } catch (ValidationException $exception) {
                $this->assertStringContainsString('Baris 3', $exception->errors()['file'][0]);
            }
        } finally {
            @unlink($path);
        }

        $this->assertDatabaseCount('assets', 0);
    }

    private function createCsv(string $contents): string
    {
        $path = tempnam(sys_get_temp_dir(), 'inventory-import-');
        file_put_contents($path, $contents);

        return $path;
    }
}
