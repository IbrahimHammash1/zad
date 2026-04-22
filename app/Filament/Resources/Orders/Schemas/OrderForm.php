<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\DeliveryAgent;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Operational Controls')
                    ->schema([
                        Select::make('delivery_agent_id')
                            ->label('Delivery Agent')
                            ->relationship(
                                name: 'deliveryAgent',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->where('is_active', true),
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Unassigned'),
                        Select::make('status')
                            ->options(fn (?Order $record): array => OrderStatus::options($record?->status))
                            ->helperText('Only valid workflow transitions are accepted. Assigned, In Progress, and Delivered require an active delivery agent.')
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Recipient Information')
                    ->schema([
                        TextInput::make('recipient_name')->disabled(),
                        TextInput::make('recipient_phone')->disabled(),
                        TextInput::make('currency')->disabled(),
                        Textarea::make('delivery_address')
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
