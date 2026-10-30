<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                    ->required(),
                TextInput::make('slug')
                    ->readOnly()
                    ->unique(ignoreRecord: true)
                    ->helperText('Otomatis dibuat oleh sistem dari nama kategori.'),
                Select::make('type')
                    ->options(['asset' => 'Asset', 'supply' => 'Supply', 'all' => 'All'])
                    ->default('all')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
