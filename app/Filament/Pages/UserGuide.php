<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class UserGuide extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Panduan Sistem (SOP)';

    protected static string|\UnitEnum|null $navigationGroup = 'Bantuan & SOP';

    protected static ?string $title = 'Panduan Operasional & Standar Prosedur (SOP) Sistem';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.user-guide';
}
