<?php

namespace App\Filament\Resources\DeliveryAgents\Pages;

use App\Filament\Resources\DeliveryAgents\DeliveryAgentResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDeliveryAgent extends ViewRecord
{
    protected static string $resource = DeliveryAgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('deactivate')
                ->label('Deactivate')
                ->icon('heroicon-o-archive-box')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->is_active)
                ->action(fn () => $this->record->update(['is_active' => false])),
            Action::make('activate')
                ->label('Activate')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => ! $this->record->is_active)
                ->action(fn () => $this->record->update(['is_active' => true])),
        ];
    }
}
