<?php

namespace App\Filament\Resources\DeliveryAgents\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class DeliveryAgentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Delivery Agent Details')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('phone')
                            ->placeholder('No phone'),
                        IconEntry::make('is_active')
                            ->boolean(),
                        TextEntry::make('notes')
                            ->placeholder('No notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Operational Orders')
                    ->description('Current assigned work without payment-sensitive data.')
                    ->schema([
                        RepeatableEntry::make('operationalOrders')
                            ->contained(false)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('Order ID'),
                                TextEntry::make('status')
                                    ->badge(),
                                TextEntry::make('recipient_name')
                                    ->label('Recipient'),
                                TextEntry::make('recipient_phone')
                                    ->label('Recipient Phone')
                                    ->placeholder('No phone'),
                                TextEntry::make('delivery_address')
                                    ->columnSpanFull(),
                                TextEntry::make('notes')
                                    ->placeholder('No notes')
                                    ->columnSpanFull(),
                                RepeatableEntry::make('orderBaskets')
                                    ->label('Ordered Baskets')
                                    ->schema([
                                        TextEntry::make('basket_name')
                                            ->label('Basket'),
                                        TextEntry::make('store_name')
                                            ->label('Store'),
                                        TextEntry::make('quantity'),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
