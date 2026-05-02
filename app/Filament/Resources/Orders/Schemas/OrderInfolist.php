<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Overview')
                    ->schema([
                        TextEntry::make('id'),
                        TextEntry::make('customer.full_name')
                            ->label('Customer'),
                        TextEntry::make('customer.user.email')
                            ->label('Customer Email')
                            ->placeholder('No email'),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('deliveryAgent.name')
                            ->label('Delivery Agent')
                            ->placeholder('Unassigned'),
                        TextEntry::make('payment.status')
                            ->label('Payment Status')
                            ->badge()
                            ->placeholder('No payment record'),
                        TextEntry::make('paid_at')
                            ->dateTime()
                            ->placeholder('Not paid'),
                    ])
                    ->columns(3),
                Section::make('Recipient Information')
                    ->schema([
                        TextEntry::make('recipient_name'),
                        TextEntry::make('recipient_phone'),
                        TextEntry::make('currency'),
                        TextEntry::make('delivery_address')
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->placeholder('No notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
                Section::make('Ordered Baskets')
                    ->schema([
                        RepeatableEntry::make('orderBaskets')
                            ->schema([
                                TextEntry::make('basket_name')
                                    ->label('Basket'),
                                TextEntry::make('store_name')
                                    ->label('Store'),
                                TextEntry::make('quantity'),
                                TextEntry::make('basket_price')
                                    ->money('USD')
                                    ->label('Price'),
                            ])
                            ->columns(4),
                    ]),
                Section::make('Status History')
                    ->schema([
                        RepeatableEntry::make('statusHistories')
                            ->schema([
                                TextEntry::make('from_status')
                                    ->badge()
                                    ->placeholder('Start'),
                                TextEntry::make('to_status')
                                    ->badge(),
                                TextEntry::make('changedBy.name')
                                    ->label('Changed By')
                                    ->placeholder('System'),
                                TextEntry::make('changed_at')
                                    ->dateTime(),
                                TextEntry::make('notes')
                                    ->placeholder('No notes')
                                    ->columnSpanFull(),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }
}
