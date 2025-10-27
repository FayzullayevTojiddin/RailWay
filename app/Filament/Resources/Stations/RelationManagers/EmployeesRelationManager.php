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
        return EmployeeForm::configure($schema);
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