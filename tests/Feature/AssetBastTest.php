<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssetBastTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_asset_bast_with_asset_details_and_photo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('assets/laptop.jpg', 'image-content');

        $asset = Asset::create([
            'asset_code' => 'PKM-AST-BAST-001',
            'name' => 'Laptop Kantor',
            'brand' => 'Lenovo ThinkPad',
            'condition' => 'Baik',
            'pic_name' => 'Budi Santoso',
            'image' => 'assets/laptop.jpg',
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('assets.bast', $asset));

        $response->assertOk()
            ->assertViewIs('bast-document')
            ->assertSee('PT PAKUAN KARYA MANDIRI')
            ->assertSee('BERITA ACARA SERAH TERIMA INVENTARIS KANTOR (BAST)')
            ->assertSee('PKM-AST-BAST-001')
            ->assertSee('Lenovo ThinkPad')
            ->assertSee('Budi Santoso')
            ->assertSee('merawat aset dengan baik')
            ->assertSee('Yang Menyerahkan')
            ->assertSee('Yang Menerima')
            ->assertSee('HRD / Direktur')
            ->assertSee(Storage::disk('public')->url('assets/laptop.jpg'));
    }

    public function test_bast_route_requires_authentication(): void
    {
        $asset = Asset::create([
            'asset_code' => 'PKM-AST-BAST-002',
            'name' => 'Printer',
            'condition' => 'Baik',
        ]);

        $this->get(route('assets.bast', $asset))
            ->assertRedirect(route('filament.admin.auth.login'));
    }
}
