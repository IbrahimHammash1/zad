<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Details')
                    ->schema([
                        TextEntry::make('id'),
                        TextEntry::make('order_id')
                            ->label('Order ID'),
                        TextEntry::make('provider'),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('amount')
                            ->money('USD'),
                        TextEntry::make('currency'),
                        TextEntry::make('provider_reference')
                            ->label('Provider Reference')
                            ->placeholder('No external reference'),
                        TextEntry::make('paid_at')
                            ->dateTime()
                            ->placeholder('Not paid'),
                        TextEntry::make('created_at')
                            ->dateTime(),
                    ])
                    ->columns(3),
            ]);
    }
}
