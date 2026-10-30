<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserGuideTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_the_system_user_guide(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staff']))
            ->get(route('filament.admin.pages.user-guide'))
            ->assertOk()
            ->assertSee('Panduan Operasional Sistem Inventaris')
            ->assertSee('Manajemen Aset Kantor')
            ->assertSee('Peminjaman Fasilitas Kantor')
            ->assertSee('Logistik &amp; Perlengkapan ATK', false)
            ->assertSee('Pemeliharaan &amp; Jadwal Servis', false)
            ->assertSee('Pelepasan Aset &amp; Dokumen BAP', false)
            ->assertSee('Kartu Inventaris Ruangan (KIR)')
            ->assertSee('Kontak Administrator / IT Support PT Pakuan Karya Mandiri')
            ->assertSee('body * {', false)
            ->assertSee('.guide-page,', false)
            ->assertSee('visibility: visible !important;', false);
    }

    public function test_unknown_roles_cannot_access_the_filament_panel(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'operator']))
            ->get(route('filament.admin.pages.user-guide'))
            ->assertForbidden();
    }
}
