<?php

namespace App\Filament\Resources\Stations\RelationManagers;

use App\Filament\Resources\Employees\Schemas\EmployeeForm;
use App\Filament\Resources\Employees\Tables\EmployeesTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Illuminate\Contracts\View\View;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';
    protected static ?string $title = 'Xodimlar';
    protected static ?string $modelLabel = 'Xodim';
    protected static ?string $pluralModelLabel = 'Xodimlar';

    public function form(Schema $schema): Schema
    {
        $schema = EmployeeForm::configure($schema);
        
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
        $table = EmployeesTable::configure($table);
        
        return $table
            ->headerActions([
                CreateAction::make()->label('Yangi xodim'),
            ]);
    }
}