<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\DatabaseNotification;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static bool $isDiscovered = false;

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        if (! Asset::query()->whereDate('next_maintenance_date', '<=', today())->exists()) {
            return;
        }

        $alreadyNotifiedToday = $user->notifications()
            ->where('type', DatabaseNotification::class)
            ->whereDate('created_at', today())
            ->where('data->title', 'Jadwal Pemeliharaan Aset')
            ->exists();

        if ($alreadyNotifiedToday) {
            return;
        }

        Notification::make()
            ->title('Jadwal Pemeliharaan Aset')
            ->body('Terdapat aset kantor yang memerlukan servis atau pemeriksaan rutin.')
            ->danger()
            ->icon(Heroicon::OutlinedExclamationTriangle)
            ->actions([
                Action::make('viewAssets')
                    ->label('Lihat Aset')
                    ->url(AssetResource::getUrl('index', ['tab' => 'perlu-servis-perbaikan'])),
            ])
            ->sendToDatabase($user);
    }
}
