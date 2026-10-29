<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\StockMutation;
use App\Models\Supply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OfficialReportTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_authenticated_user_can_open_the_official_asset_report(): void
    {
        $this->actingAs(User::factory()->create());

        Asset::create([
            'asset_code' => 'PKM-AST-001',
            'name' => 'Laptop Kantor',
            'size' => 'All Size',
            'quantity' => 2,
            'status' => 'Digunakan',
            'condition' => 'Baik',
            'price' => 10000000,
            'pic_name' => 'Budi Santoso',
        ]);

        $response = $this->get(route('reports.assets'));

        $response->assertOk()
            ->assertViewIs('reports.official')
            ->assertSee('PT PAKUAN KARYA MANDIRI')
            ->assertSee('Rekapitulasi Inventaris Aset Kantor')
            ->assertSee('PKM-AST-001')
            ->assertSee('Laptop Kantor')
            ->assertSee('All Size')
            ->assertSee('Digunakan')
            ->assertSee('Rp 10.000.000')
            ->assertSee('Budi Santoso')
            ->assertSee('Dibuat Oleh (Admin/Pengelola)')
            ->assertSee('Mengetahui (Pimpinan/Direktur)');
    }

    public function test_monthly_supply_report_contains_only_current_month_mutations_and_totals(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 10, 15, 10));
        $this->actingAs(User::factory()->create());

        $supply = Supply::create([
            'item_code' => 'ATK-001',
            'name' => 'Kertas A4',
            'stock' => 10,
            'unit' => 'rim',
        ]);
        StockMutation::create([
            'supply_id' => $supply->id,
            'type' => 'in',
            'quantity' => 4,
            'mutation_date' => '2026-10-05',
        ]);
        StockMutation::create([
            'supply_id' => $supply->id,
            'type' => 'out',
            'quantity' => 2,
            'mutation_date' => '2026-10-10',
        ]);

        $previousMonthSupply = Supply::create([
            'item_code' => 'ATK-OLD',
            'name' => 'Data Periode Lalu',
            'stock' => 0,
            'unit' => 'pcs',
        ]);
        StockMutation::create([
            'supply_id' => $previousMonthSupply->id,
            'type' => 'in',
            'quantity' => 9,
            'mutation_date' => '2026-09-30',
        ]);

        $response = $this->get(route('reports.stock-mutations'));

        $response->assertOk()
            ->assertViewIs('reports.official')
            ->assertSee('Rekapitulasi Pengeluaran & Pemasukan ATK Bulanan')
            ->assertSee('Periode: 10/2026')
            ->assertSee('Kertas A4')
            ->assertSee('Total barang masuk')
            ->assertSee('4 unit')
            ->assertSee('Total barang keluar')
            ->assertSee('2 unit')
            ->assertDontSee('Data Periode Lalu');
    }
}
