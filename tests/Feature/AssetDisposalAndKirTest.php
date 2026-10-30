<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetDisposal;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetDisposalAndKirTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_disposal_bap_for_disposed_asset(): void
    {
        $asset = Asset::create([
            'asset_code' => 'PKM-AST-BAP-001',
            'name' => 'Printer Lama',
            'brand' => 'Canon',
            'condition' => 'Rusak Berat',
            'status' => 'Rusak/Afkir',
        ]);
        $asset->disposal()->create([
            'disposal_date' => '2026-10-30',
            'disposal_method' => 'Dijual / Dilelang',
            'disposal_amount' => 250000,
            'reason' => 'Biaya perbaikan melebihi nilai aset.',
            'approved_by' => 'Direktur Utama',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('assets.disposal.bap', $asset))
            ->assertOk()
            ->assertViewIs('bap-document')
            ->assertSee('BERITA ACARA PENGHAPUSAN ASET INVENTARIS')
            ->assertSee('PKM-AST-BAP-001')
            ->assertSee('Dijual / Dilelang')
            ->assertSee('Rp 250.000')
            ->assertSee('Biaya perbaikan melebihi nilai aset.')
            ->assertSee('Pengelola Aset')
            ->assertSee('Keuangan')
            ->assertSee('Direktur Utama');
    }

    public function test_disposal_bap_returns_not_found_when_asset_has_no_disposal_record(): void
    {
        $asset = Asset::create([
            'asset_code' => 'PKM-AST-BAP-002',
            'name' => 'Laptop',
            'condition' => 'Baik',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('assets.disposal.bap', $asset))
            ->assertNotFound();
    }

    public function test_authenticated_user_can_print_room_inventory_card(): void
    {
        $room = Room::create([
            'code' => 'R-101',
            'name' => 'Ruang Administrasi',
            'location_floor' => 'Lantai 1',
            'description' => 'Ruang kerja administrasi',
        ]);
        $room->assets()->create([
            'asset_code' => 'PKM-AST-KIR-001',
            'name' => 'Komputer',
            'brand' => 'Lenovo',
            'condition' => 'Rusak Ringan',
            'status' => 'Digunakan',
            'pic_name' => 'Siti Rahma',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('rooms.kir', $room))
            ->assertOk()
            ->assertViewIs('kir-document')
            ->assertSee('KARTU INVENTARIS RUANGAN (KIR)')
            ->assertSee('R-101')
            ->assertSee('Ruang Administrasi')
            ->assertSee('PKM-AST-KIR-001')
            ->assertSee('Komputer - Lenovo')
            ->assertSee('>RR<', false)
            ->assertSee('Siti Rahma')
            ->assertSee('Penanggung Jawab Ruangan')
            ->assertSee('Bagian Umum &amp; Logistik (GA)', false);
    }

    public function test_disposal_relation_cascades_when_asset_is_deleted(): void
    {
        $asset = Asset::create([
            'asset_code' => 'PKM-AST-BAP-003',
            'name' => 'Scanner',
            'condition' => 'Rusak Berat',
        ]);

        AssetDisposal::create([
            'asset_id' => $asset->id,
            'disposal_date' => '2026-10-30',
            'disposal_method' => 'Dimusnahkan',
            'reason' => 'Tidak layak digunakan.',
            'approved_by' => 'Direktur',
        ]);

        $asset->delete();

        $this->assertDatabaseCount('asset_disposals', 0);
    }
}
