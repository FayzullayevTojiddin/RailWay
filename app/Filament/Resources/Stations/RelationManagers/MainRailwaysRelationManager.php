<?php

namespace App\Filament\Resources\Stations\RelationManagers;

use App\Filament\Resources\MainRailways\Schemas\MainRailwayForm;
use App\Filament\Resources\MainRailways\Tables\MainRailwaysTable;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MainRailwaysRelationManager extends RelationManager
{
    protected static string $relationship = 'mainRailways';
    protected static ?string $title = 'Temir yo\'llar';
    protected static ?string $modelLabel = 'Temir yo\'l';
    protected static ?string $pluralModelLabel = 'Temir yo\'llar';

    public function form(Schema $schema): Schema
    {
        $schema = MainRailwayForm::configure($schema);
        
        // Station relation manager da bo'lganimiz uchun station_id fieldni yashiramiz
        // Chunki station avtomatik owner record dan olinadi
        $components = collect($schema->getComponents())
            ->map(function ($section) {
                if (method_exists($section, 'getChildComponents')) {
                    $childComponents = collect($section->getChildComponents())
                        ->filter(fn ($component) => 
                            !method_exists($component, 'getName') || 
                            $component->getName() !== 'station_id'
                        )
                        ->toArray();
                    
                    return $section->schema($childComponents);
                }
                return $section;
            })
            ->toArray();
        
        return $schema->components($components);
    }

    public function table(Table $table): Table
    {
        $table = MainRailwaysTable::configure($table);
        
        return $table
            ->headerActions([
                CreateAction::make()->label('Yangi temir yo\'l'),
            ]);
    }
}
