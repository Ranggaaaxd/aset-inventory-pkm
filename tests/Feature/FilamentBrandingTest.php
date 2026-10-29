<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\CustomLogin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_admin_login_displays_branding_features_and_login_fields(): void
    {
        $logoUrl = asset('images/logo-pkm.png');

        $this->get(route('filament.admin.auth.login'))
            ->assertOk()
            ->assertSee('PT Pakuan Karya Mandiri')
            ->assertSee('Sistem Manajemen Aset &amp; Logistik Terpadu', false)
            ->assertSee('Pelacakan Real-Time')
            ->assertSee('Akuntabilitas BAST')
            ->assertSee('Kalkulasi Nilai Depresiasi')
            ->assertSee('wire:submit="authenticate"', false)
            ->assertSee('class="custom-login-page"', false)
            ->assertSee('wire:loading.flex', false)
            ->assertSee('wire:target="authenticate"', false)
            ->assertSee('Memproses login...')
            ->assertSee('wire:model="data.email"', false)
            ->assertSee('wire:model="data.password"', false)
            ->assertSee('wire:model="data.remember"', false)
            ->assertSee('Masuk ke Portal')
            ->assertSee('© 2026 PT Pakuan Karya Mandiri. All rights reserved.')
            ->assertSee($logoUrl, false);
    }

    public function test_custom_admin_login_authenticates_and_redirects_to_the_admin_panel(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'correct-password',
        ]);

        Livewire::test(CustomLogin::class)
            ->set('data.email', $user->email)
            ->set('data.password', 'correct-password')
            ->call('authenticate')
            ->assertRedirect('http://127.0.0.1:8000/admin');

        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_custom_admin_login_shows_an_error_for_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'correct-password',
        ]);

        Livewire::test(CustomLogin::class)
            ->set('data.email', 'admin@example.test')
            ->set('data.password', 'incorrect-password')
            ->call('authenticate')
            ->assertHasErrors(['data.email'])
            ->assertSee('These credentials do not match our records.');
    }
}
