<?php

namespace App\Filament\Resources\DeliveryAgents\Pages;

use App\Filament\Resources\DeliveryAgents\DeliveryAgentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryAgents extends ListRecords
{
    protected static string $resource = DeliveryAgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
