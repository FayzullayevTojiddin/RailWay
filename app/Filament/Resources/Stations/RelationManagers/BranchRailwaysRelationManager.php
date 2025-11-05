<?php

namespace App\Filament\Resources\Stations\RelationManagers;

use App\Filament\Resources\BranchRailways\Schemas\BranchRailwayForm;
use App\Filament\Resources\BranchRailways\Tables\BranchRailwaysTable;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BranchRailwaysRelationManager extends RelationManager
{
    protected static string $relationship = 'branchRailways';
    protected static ?string $title = 'Shaxobcha yo\'llari';
    protected static ?string $modelLabel = 'Shaxobcha yo\'l';
    protected static ?string $pluralModelLabel = 'Shaxobcha yo\'llari';

    public function form(Schema $schema): Schema
    {
        $schema = BranchRailwayForm::configure($schema);
        
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
        $table = BranchRailwaysTable::configure($table);
        
        return $table
            ->headerActions([
                CreateAction::make()->label('Yangi shaxobcha yo\'l'),
            ]);
    }
}
