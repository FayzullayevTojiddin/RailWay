<?php

namespace App\Filament\Resources\Stations\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Filament\Resources\Cadastres\Schemas\CadastreForm;
use App\Filament\Resources\Cadastres\Tables\CadastresTable;

class CadastresRelationManager extends RelationManager
{
    protected static string $relationship = 'cadastres';

    protected static ?string $title = "Kadastrlar";

    protected static ?string $modelLabel = "Kadastr";
    
    protected static ?string $pluralModelLabel = "Kadastrlar";

    public function form(Schema $schema): Schema
    {
        $schema = CadastreForm::configure($schema);
        
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
        $table = CadastresTable::configure($table);
        return $table
            ->headerActions([
                CreateAction::make()->label("Yangi qo'shish")
            ]);;
    }
}
