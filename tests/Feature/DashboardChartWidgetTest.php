<?php

namespace Tests\Feature;

use App\Filament\Widgets\AssetConditionChartWidget;
use App\Filament\Widgets\MonthlySupplyUsageChartWidget;
use App\Models\Asset;
use App\Models\StockMutation;
use App\Models\Supply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use ReflectionMethod;
use Tests\TestCase;

class DashboardChartWidgetTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_asset_condition_chart_shows_all_conditions_including_empty_categories(): void
    {
        Asset::create(['asset_code' => 'AST-001', 'name' => 'Laptop', 'condition' => 'Baik']);
        Asset::create(['asset_code' => 'AST-002', 'name' => 'Printer', 'condition' => 'Baik']);
        Asset::create(['asset_code' => 'AST-003', 'name' => 'Scanner', 'condition' => 'Rusak Ringan']);

        $data = $this->getWidgetData(AssetConditionChartWidget::class);

        $this->assertSame(['Baik', 'Rusak Ringan', 'Rusak Berat'], $data['labels']);
        $this->assertSame([2, 1, 0], $data['datasets'][0]['data']);
        $this->assertSame(['#16a34a', '#eab308', '#dc2626'], $data['datasets'][0]['backgroundColor']);
    }

    public function test_monthly_supply_chart_sums_only_out_mutations_in_the_current_year(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 15));

        $supply = Supply::create([
            'item_code' => 'ATK-001',
            'name' => 'Kertas',
            'stock' => 100,
        ]);

        foreach ([
            ['type' => 'out', 'quantity' => 3, 'mutation_date' => '2026-01-05'],
            ['type' => 'out', 'quantity' => 4, 'mutation_date' => '2026-01-20'],
            ['type' => 'in', 'quantity' => 8, 'mutation_date' => '2026-01-22'],
            ['type' => 'out', 'quantity' => 6, 'mutation_date' => '2025-12-30'],
            ['type' => 'out', 'quantity' => 9, 'mutation_date' => '2027-01-02'],
        ] as $mutation) {
            StockMutation::create([
                'supply_id' => $supply->id,
                ...$mutation,
            ]);
        }

        $data = $this->getWidgetData(MonthlySupplyUsageChartWidget::class);

        $this->assertSame('Jan 2026', $data['labels'][0]);
        $this->assertCount(12, $data['labels']);
        $this->assertSame(7, $data['datasets'][0]['data'][0]);
        $this->assertSame(array_fill(0, 11, 0), array_slice($data['datasets'][0]['data'], 1));
    }

    private function getWidgetData(string $widgetClass): array
    {
        $widget = new $widgetClass;
        $method = new ReflectionMethod($widgetClass, 'getData');

        return $method->invoke($widget);
    }
}
