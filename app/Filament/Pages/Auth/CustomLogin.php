<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login;

class CustomLogin extends Login
{
    protected string $view = 'filament.pages.auth.custom-login';

    protected static string $layout = 'filament.pages.auth.custom-login-layout';

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Masuk ke Portal')
            ->submit('authenticate');
    }
}
