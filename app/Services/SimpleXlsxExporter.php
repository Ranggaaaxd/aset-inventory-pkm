<?php

namespace App\Services;

use ZipArchive;
use Exception;

class SimpleXlsxExporter
{
    /**
     * Create an Excel (.xlsx) file at the given file path.
     *
     * @param string $filePath Absolute file path to write the .xlsx file
     * @param array<string> $headers Header titles
     * @param array<array<mixed>> $rows Row data
     * @param string $sheetName Worksheet title
     */
    public static function create(string $filePath, array $headers, array $rows, string $sheetName = 'Laporan'): void
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Gagal membuat file excel di: {$filePath}");
        }

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // 3. xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. xl/workbook.xml
        $cleanSheetName = htmlspecialchars(mb_substr($sheetName, 0, 31), ENT_XML1, 'UTF-8');
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . $cleanSheetName . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. xl/styles.xml
        // Style 0: default, Style 1: Header (bold, gray background, border), Style 2: Number/Currency format
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<numFmts count="1"><numFmt numFmtId="164" formatCode="#,##0"/></numFmts>'
            . '<fonts count="2">'
            . '<font><sz val="11"/><color rgb="FF1F2937"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
            . '</fonts>'
            . '<fills count="3">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF3B82F6"/></patternFill></fill>' // Blue-500 header
            . '</fills>'
            . '<borders count="2">'
            . '<border><left/><right/><top/><bottom/></border>'
            . '<border>'
            . '<left style="thin"><color rgb="FFD1D5DB"/></left>'
            . '<right style="thin"><color rgb="FFD1D5DB"/></right>'
            . '<top style="thin"><color rgb="FFD1D5DB"/></top>'
            . '<bottom style="thin"><color rgb="FFD1D5DB"/></bottom>'
            . '</border>'
            . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="3">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0"/>' // 0: Normal data with thin border
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/>' // 1: Header
            . '<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1"/>' // 2: Number with separator
            . '</cellXfs>'
            . '</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);

        // Calculate approximate column widths based on contents
        $colWidths = [];
        foreach ($headers as $ci => $h) {
            $colWidths[$ci] = max(12, mb_strlen((string)$h) + 4);
        }
        foreach (array_slice($rows, 0, 50) as $row) {
            foreach ($row as $ci => $val) {
                $len = mb_strlen((string)($val ?? '')) + 3;
                if (!isset($colWidths[$ci]) || $len > $colWidths[$ci]) {
                    $colWidths[$ci] = min(40, max($colWidths[$ci] ?? 12, $len));
                }
            }
        }

        // 6. xl/worksheets/sheet1.xml
        $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<cols>';
        foreach ($colWidths as $ci => $w) {
            $colNum = $ci + 1;
            $sheet .= '<col min="' . $colNum . '" max="' . $colNum . '" width="' . $w . '" customWidth="1"/>';
        }
        $sheet .= '</cols><sheetData>';

        // Header Row
        $sheet .= '<row r="1" ht="26" customHeight="1">';
        $ci = 0;
        foreach ($headers as $header) {
            $colLetter = self::colLetter($ci);
            $cleanVal = self::cleanXmlString((string)$header);
            $sheet .= '<c r="' . $colLetter . '1" t="inlineStr" s="1"><is><t>' . $cleanVal . '</t></is></c>';
            $ci++;
        }
        $sheet .= '</row>';

        // Data Rows
        $ri = 2;
        foreach ($rows as $row) {
            $sheet .= '<row r="' . $ri . '" ht="20" customHeight="1">';
            $ci = 0;
            foreach ($row as $val) {
                $colLetter = self::colLetter($ci);
                if (is_int($val) || is_float($val)) {
                    $sheet .= '<c r="' . $colLetter . $ri . '" s="2"><v>' . $val . '</v></c>';
                } elseif (is_numeric($val) && !is_string($val)) {
                    $sheet .= '<c r="' . $colLetter . $ri . '" s="2"><v>' . $val . '</v></c>';
                } else {
                    $cleanVal = self::cleanXmlString((string)($val ?? ''));
                    $sheet .= '<c r="' . $colLetter . $ri . '" t="inlineStr" s="0"><is><t>' . $cleanVal . '</t></is></c>';
                }
                $ci++;
            }
            $sheet .= '</row>';
            $ri++;
        }

        $sheet .= '</sheetData></worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet);

        $zip->close();
    }

    private static function colLetter(int $index): string
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr($index % 26 + 65) . $letter;
            $index = intdiv($index, 26) - 1;
        }
        return $letter;
    }

    private static function cleanXmlString(string $value): string
    {
        // Strip control characters that are invalid in XML 1.0
        $stripped = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
        return htmlspecialchars($stripped ?? '', ENT_XML1, 'UTF-8');
    }
}

