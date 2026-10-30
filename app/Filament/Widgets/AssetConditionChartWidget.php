<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;

class AssetConditionChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Proporsi Kondisi Aset Kantor';

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '270px';

    protected function getData(): array
    {
        $conditions = ['Baik', 'Rusak Ringan', 'Rusak Berat'];
        $assetCounts = Asset::query()
            ->select('condition')
            ->selectRaw('COUNT(*) as total')
            ->whereIn('condition', $conditions)
            ->groupBy('condition')
            ->pluck('total', 'condition');

        return [
            'datasets' => [
                [
                    'data' => array_map(
                        fn (string $condition): int => (int) ($assetCounts[$condition] ?? 0),
                        $conditions,
                    ),
                    'backgroundColor' => ['#16a34a', '#eab308', '#dc2626'],
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => $conditions,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
