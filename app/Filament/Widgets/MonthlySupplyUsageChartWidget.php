<?php

namespace App\Filament\Widgets;

use App\Models\StockMutation;
use Filament\Widgets\ChartWidget;

class MonthlySupplyUsageChartWidget extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 99;

    protected ?string $heading = 'Tren Pemakaian Perlengkapan & ATK Bulanan';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $year = now()->year;
        $monthlyTotals = StockMutation::query()
            ->where('type', 'out')
            ->whereBetween('mutation_date', [
                now()->startOfYear()->toDateString(),
                now()->endOfYear()->toDateString(),
            ])
            ->selectRaw('SUBSTR(mutation_date, 6, 2) as month, SUM(quantity) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $months = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah barang keluar',
                    'data' => array_map(
                        fn (int $month): int => (int) ($monthlyTotals[sprintf('%02d', $month)] ?? 0),
                        range(1, 12),
                    ),
                    'backgroundColor' => '#0f3b6c',
                    'borderColor' => '#0f3b6c',
                ],
            ],
            'labels' => array_map(fn (string $month): string => "{$month} {$year}", $months),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
