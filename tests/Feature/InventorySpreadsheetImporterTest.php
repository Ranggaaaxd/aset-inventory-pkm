<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Room;
use App\Models\Supply;
use App\Services\AssetTemplateDownload;
use App\Services\InventorySpreadsheetImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
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

    public function test_it_imports_xlsx_purchase_dates_and_fuzzy_category_and_room_names(): void
    {
        Category::create(['name' => 'Elektronik Kantor', 'type' => 'asset']);
        Room::create(['code' => 'RR', 'name' => 'Ruang Rapat Utama']);

        $spreadsheet = new Spreadsheet;
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray([
            ['name', 'category_name', 'room_name', 'brand', 'condition', 'price', 'status', 'pic_name', 'purchase_date', 'tax_group'],
            ['Printer', 'elektronik', 'rapat', 'HP', 'Baik', 1250000, 'Digunakan', 'Siti', Date::PHPToExcel(new \DateTimeImmutable('2026-10-30')), 'Golongan 1'],
            ['Copier', null, null, null, null, null, null, null, null, 'Golongan 2'],
            ['Monitor', null, null, null, null, null, null, null, null, 'Non-Depresiasi'],
        ]);
        $worksheet->getStyle('I2')->getNumberFormat()->setFormatCode('yyyy-mm-dd');

        $temporaryPath = tempnam(sys_get_temp_dir(), 'inventory-import-');
        $path = $temporaryPath.'.xlsx';
        unlink($temporaryPath);
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        try {
            $imported = app(InventorySpreadsheetImporter::class)->importAssets($path, 'xlsx');
        } finally {
            @unlink($path);
        }

        $asset = Asset::query()->where('name', 'Printer')->firstOrFail();

        $this->assertSame(3, $imported);
        $this->assertSame('Elektronik Kantor', $asset->category->name);
        $this->assertSame('Ruang Rapat Utama', $asset->room->name);
        $this->assertSame('2026-10-30', $asset->purchase_date->toDateString());
        $this->assertNotEmpty($asset->asset_code);
        $this->assertSame('Golongan 1', $asset->tax_group);
        $this->assertSame(4, $asset->useful_life_years);
        $this->assertDatabaseHas('assets', [
            'name' => 'Copier',
            'tax_group' => 'Golongan 2',
            'useful_life_years' => 8,
        ]);
        $this->assertDatabaseHas('assets', [
            'name' => 'Monitor',
            'tax_group' => 'Non-Depresiasi',
            'useful_life_years' => 0,
        ]);
    }

    public function test_asset_template_is_an_xlsx_with_dropdowns_and_date_guidance(): void
    {
        Category::create(['name' => 'Elektronik', 'type' => 'asset']);
        Room::create(['code' => 'RR', 'name' => 'Ruang Rapat']);

        $response = AssetTemplateDownload::download();
        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertStringContainsString('template_aset_pkm.xlsx', $response->headers->get('Content-Disposition'));

        ob_start();
        $response->sendContent();
        $content = (string) ob_get_clean();
        $path = tempnam(sys_get_temp_dir(), 'asset-template-');
        file_put_contents($path, $content);

        try {
            $spreadsheet = IOFactory::load($path);
        } finally {
            @unlink($path);
        }

        $worksheet = $spreadsheet->getActiveSheet();
        $this->assertSame([
            'name', 'category_name', 'room_name', 'brand', 'condition', 'price', 'status', 'pic_name', 'purchase_date', 'tax_group',
        ], $worksheet->rangeToArray('A1:J1')[0]);
        $this->assertSame('Proyektor Epson', $worksheet->getCell('A2')->getValue());
        $this->assertSame('Golongan 1', $worksheet->getCell('J2')->getValue());
        $categoryValidation = $worksheet->getCell('B2')->getDataValidation();
        $this->assertSame('"Elektronik"', $categoryValidation->getFormula1());
        $this->assertSame('"Ruang Rapat"', $worksheet->getCell('C100')->getDataValidation()->getFormula1());
        $this->assertSame('"Elektronik"', $worksheet->getCell('B100')->getDataValidation()->getFormula1());
        $this->assertNotSame($categoryValidation, $worksheet->getCell('B3')->getDataValidation());
        $this->assertSame('"Baik,Rusak Ringan,Rusak Berat"', $worksheet->getCell('E2')->getDataValidation()->getFormula1());
        $this->assertSame(
            '"Tersedia di Gudang,Digunakan,Dipinjam,Rusak/Afkir"',
            $worksheet->getCell('G2')->getDataValidation()->getFormula1(),
        );
        $this->assertSame('"Golongan 1,Golongan 2,Non-Depresiasi"', $worksheet->getCell('J100')->getDataValidation()->getFormula1());
        foreach (['B2', 'C3', 'E4', 'G100', 'J100'] as $cell) {
            $validation = $worksheet->getCell($cell)->getDataValidation();
            $this->assertSame(DataValidation::TYPE_LIST, $validation->getType());
            $this->assertSame(DataValidation::STYLE_STOP, $validation->getErrorStyle());
            $this->assertFalse($validation->getAllowBlank());
            $this->assertTrue($validation->getShowInputMessage());
            $this->assertTrue($validation->getShowErrorMessage());
            $this->assertTrue($validation->getShowDropDown());
        }
        $this->assertStringContainsString('YYYY-MM-DD', $worksheet->getComment('I1')->getText()->getPlainText());

        $spreadsheet->disconnectWorksheets();
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
