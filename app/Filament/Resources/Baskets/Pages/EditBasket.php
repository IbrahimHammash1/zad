<?php

namespace App\Filament\Resources\Baskets\Pages;

use App\Filament\Resources\Baskets\BasketResource;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBasket extends EditRecord
{
    protected static string $resource = BasketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            Action::make('archive')
                ->label('Archive')
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
