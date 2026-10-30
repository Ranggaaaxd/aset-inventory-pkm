<?php

namespace Tests\Feature;

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\Assets\Pages\ListAssets;
use App\Models\Asset;
use App\Models\User;
use Filament\Notifications\DatabaseNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AssetMaintenanceReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_maintenance_tab_includes_due_and_damaged_assets(): void
    {
        $dueAsset = Asset::create([
            'name' => 'Aset Jatuh Tempo',
            'condition' => 'Baik',
            'next_maintenance_date' => today()->addDays(3),
        ]);
        $damagedAsset = Asset::create([
            'name' => 'Aset Rusak',
            'condition' => 'Rusak Ringan',
        ]);
        Asset::create([
            'name' => 'Aset Belum Jatuh Tempo',
            'condition' => 'Baik',
            'next_maintenance_date' => today()->addDays(8),
        ]);

        $tabs = app(ListAssets::class)->getTabs();
        $maintenanceTab = $tabs['perlu-servis-perbaikan'];
        $maintenanceAssets = $maintenanceTab->modifyQuery(Asset::query())->pluck('id')->all();

        $this->assertSame([$dueAsset->id, $damagedAsset->id], $maintenanceAssets);
        $this->assertSame('2', $maintenanceTab->getBadge());
        $this->assertSame('danger', $maintenanceTab->getBadgeColor());
        $this->assertArrayHasKey('semua', $tabs);
    }

    public function test_dashboard_sends_at_most_one_maintenance_database_notification_per_day(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Asset::create([
            'name' => 'Aset Perlu Servis',
            'condition' => 'Baik',
            'next_maintenance_date' => today(),
        ]);

        app(Dashboard::class)->mount();
        app(Dashboard::class)->mount();

        $this->assertDatabaseCount('notifications', 1);
        $notification = DB::table('notifications')->first();
        $this->assertSame(DatabaseNotification::class, $notification->type);
        $this->assertSame($user->getKey(), $notification->notifiable_id);

        $data = json_decode($notification->data, true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('Jadwal Pemeliharaan Aset', $data['title']);
        $this->assertSame('danger', $data['status']);
        $this->assertStringContainsString('tab=perlu-servis-perbaikan', $data['actions'][0]['url']);
    }
}
