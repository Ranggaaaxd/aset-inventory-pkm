<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvTemplateDownload
{
    /**
     * @param  array<int, string>  $headers
     * @param  array<int, string>  $sample
     */
    public static function download(string $filename, array $headers, array $sample): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $sample): void {
            echo "\xEF\xBB\xBF";

            $output = fopen('php://output', 'wb');

            if ($output === false) {
                throw new \RuntimeException('Tidak dapat membuka output untuk membuat template CSV.');
            }

            fputcsv($output, $headers, ';', '"', '', "\r\n");
            fputcsv($output, $sample, ';', '"', '', "\r\n");
            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
