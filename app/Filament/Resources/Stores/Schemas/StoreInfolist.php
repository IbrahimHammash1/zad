<?php

namespace App\Filament\Resources\Stores\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class StoreInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Store Details')
                    ->schema([
                        ImageEntry::make('image_url')
                            ->label('Image')
                            ->disk('public')
                            ->height(200)
                            ->columnSpanFull(),
                        TextEntry::make('name'),
                        TextEntry::make('phone')
                            ->placeholder('No phone'),
                        IconEntry::make('is_active')
                            ->boolean(),
                        TextEntry::make('address')
                            ->placeholder('No address')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Assigned Baskets')
                    ->schema([
                        TextEntry::make('baskets.name')
                            ->badge()
                            ->listWithLineBreaks()
                            ->placeholder('No baskets assigned'),
                    ]),
            ]);
    }
}
