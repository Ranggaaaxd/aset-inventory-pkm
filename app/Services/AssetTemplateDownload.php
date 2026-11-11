<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Room;
use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetTemplateDownload
{
    public static function download(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle('Template Aset');

        $headers = [
            'name',
            'category_name',
            'room_name',
            'brand',
            'condition',
            'price',
            'status',
            'pic_name',
            'purchase_date',
            'tax_group',
        ];
        $categoryNames = Category::query()->pluck('name')->all();
        $roomNames = Room::query()->pluck('name')->all();

        $worksheet->fromArray($headers, null, 'A1');

        $worksheet->fromArray([
            'Proyektor Epson',
            Category::query()->whereIn('type', ['asset', 'all'])->value('name') ?? '',
            $roomNames[0] ?? '',
            'Epson',
            'Baik',
            5000000,
            'Digunakan',
            'Siti Rahma',
            Date::PHPToExcel(new DateTimeImmutable('2026-10-30')),
            'Golongan 1',
        ], null, 'A2');
        $worksheet->getStyle('I2:I100')->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        $worksheet->getComment('I1')->getText()->createTextRun(
            'Gunakan format YYYY-MM-DD, contoh: 2026-10-30.',
        );

        $categoryValidation = self::makeListValidation('"'.implode(',', $categoryNames).'"');
        $roomValidation = self::makeListValidation('"'.implode(',', $roomNames).'"');
        $conditionValidation = self::makeListValidation('"Baik,Rusak Ringan,Rusak Berat"');
        $statusValidation = self::makeListValidation('"Tersedia di Gudang,Digunakan,Dipinjam,Rusak/Afkir"');
        $taxValidation = self::makeListValidation('"Golongan 1,Golongan 2,Non-Depresiasi"');

        for ($row = 2; $row <= 100; $row++) {
            $worksheet->getCell("B{$row}")->setDataValidation(clone $categoryValidation);
            $worksheet->getCell("C{$row}")->setDataValidation(clone $roomValidation);
            $worksheet->getCell("E{$row}")->setDataValidation(clone $conditionValidation);
            $worksheet->getCell("G{$row}")->setDataValidation(clone $statusValidation);
            $worksheet->getCell("J{$row}")->setDataValidation(clone $taxValidation);

            $dateValidation = $worksheet->getCell("I{$row}")->getDataValidation();
            $dateValidation->setType(DataValidation::TYPE_DATE);
            $dateValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $dateValidation->setAllowBlank(true);
            $dateValidation->setShowInputMessage(true);
            $dateValidation->setPromptTitle('Format tanggal');
            $dateValidation->setPrompt('Gunakan format YYYY-MM-DD, contoh: 2026-10-30.');
            $dateValidation->setFormula1('DATE(1900,1,1)');
            $dateValidation->setFormula2('DATE(9999,12,31)');
            $dateValidation->setOperator(DataValidation::OPERATOR_BETWEEN);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, 'template_aset_pkm.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private static function makeListValidation(string $formula): DataValidation
    {
        $validation = new DataValidation;
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(false);
        $validation->setShowDropDown(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowInputMessage(true);
        $validation->setFormula1($formula);

        return $validation;
    }
}
