<?php

namespace App\Filament\Resources\DeliveryAgents;

use App\Filament\Resources\DeliveryAgents\Pages\CreateDeliveryAgent;
use App\Filament\Resources\DeliveryAgents\Pages\EditDeliveryAgent;
use App\Filament\Resources\DeliveryAgents\Pages\ListDeliveryAgents;
use App\Filament\Resources\DeliveryAgents\Pages\ViewDeliveryAgent;
use App\Filament\Resources\DeliveryAgents\Schemas\DeliveryAgentForm;
use App\Filament\Resources\DeliveryAgents\Schemas\DeliveryAgentInfolist;
use App\Filament\Resources\DeliveryAgents\Tables\DeliveryAgentsTable;
use App\Models\DeliveryAgent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DeliveryAgentResource extends Resource
{
    protected static ?string $model = DeliveryAgent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return DeliveryAgentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DeliveryAgentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeliveryAgentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeliveryAgents::route('/'),
            'create' => CreateDeliveryAgent::route('/create'),
            'view' => ViewDeliveryAgent::route('/{record}'),
            'edit' => EditDeliveryAgent::route('/{record}/edit'),
        ];
    }
}
