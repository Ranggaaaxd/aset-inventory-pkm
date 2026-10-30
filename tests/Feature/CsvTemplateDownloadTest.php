<?php

namespace Tests\Feature;

use App\Services\CsvTemplateDownload;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class CsvTemplateDownloadTest extends TestCase
{
    public function test_asset_template_is_downloaded_as_utf8_csv_with_headers_and_sample_row(): void
    {
        $response = CsvTemplateDownload::download(
            'template_aset_pkm.csv',
            ['name', 'category_name', 'room_name', 'brand', 'condition', 'price', 'status', 'pic_name'],
            ['Proyektor Epson', 'Elektronik', 'Ruang Rapat', 'Epson', 'Baik', '5000000', 'Digunakan', 'Siti Rahma'],
        );
        $content = $this->getStreamedContent($response);

        $this->assertSame('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('template_aset_pkm.csv', $response->headers->get('Content-Disposition'));
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertSame([
            ['name', 'category_name', 'room_name', 'brand', 'condition', 'price', 'status', 'pic_name'],
            ['Proyektor Epson', 'Elektronik', 'Ruang Rapat', 'Epson', 'Baik', '5000000', 'Digunakan', 'Siti Rahma'],
        ], $this->parseCsv($content));
    }

    public function test_supply_template_contains_the_expected_header_and_sample_row(): void
    {
        $response = CsvTemplateDownload::download(
            'template_atk_pkm.csv',
            ['name', 'category_name', 'stock', 'min_stock', 'unit', 'notes'],
            ['Kertas A4', 'ATK', '20', '5', 'ream', 'Contoh catatan'],
        );
        $content = $this->getStreamedContent($response);

        $this->assertSame([
            ['name', 'category_name', 'stock', 'min_stock', 'unit', 'notes'],
            ['Kertas A4', 'ATK', '20', '5', 'ream', 'Contoh catatan'],
        ], $this->parseCsv($content));
    }

    private function getStreamedContent(StreamedResponse $response): string
    {
        ob_start();
        $response->sendContent();

        return (string) ob_get_clean();
    }

    /**
     * @return array<int, array<int, string|null>>
     */
    private function parseCsv(string $content): array
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, substr($content, 3));
        rewind($stream);

        $rows = [];
        while (($row = fgetcsv($stream, separator: ';', enclosure: '"', escape: '')) !== false) {
            $rows[] = $row;
        }

        fclose($stream);

        return $rows;
    }
}
