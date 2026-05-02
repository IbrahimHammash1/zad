<?php

namespace App\Filament\Resources\Baskets\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BasketInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basket Details')
                    ->schema([
                        ImageEntry::make('image_url')
                            ->label('Image')
                            ->disk('public')
                            ->height(200)
                            ->columnSpanFull(),
                        TextEntry::make('name'),
                        TextEntry::make('slug'),
                        TextEntry::make('fixed_price')
                            ->money('USD'),
                        IconEntry::make('is_active')
                            ->boolean(),
                        TextEntry::make('description')
                            ->placeholder('No description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Basket Items')
                    ->schema([
                        RepeatableEntry::make('basketItems')
                            ->schema([
                                TextEntry::make('material.name')
                                    ->label('Material'),
                                TextEntry::make('quantity'),
                                TextEntry::make('material.unit')
                                    ->label('Unit'),
                            ])
                            ->columns(3),
                    ]),
                Section::make('Approved Stores')
                    ->schema([
                        TextEntry::make('stores.name')
                            ->badge()
                            ->listWithLineBreaks()
                            ->placeholder('No approved stores'),
                    ]),
            ]);
    }
}
