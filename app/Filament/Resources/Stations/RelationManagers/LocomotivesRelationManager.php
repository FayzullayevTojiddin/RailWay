<?php

namespace App\Filament\Resources\Stations\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use App\LocomotiveType;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Filters\TabsFilter;
use App\Enums\StationType;
use Illuminate\Database\Eloquent\Model;

class LocomotivesRelationManager extends RelationManager
{
    protected static string $relationship = 'locomotives';

    protected static ?string $title = "Lokomotivlar";

    protected static ?string $modelLabel = "Lokomotiv";
    
    protected static ?string $pluralModelLabel = "Lokomotivlar";

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return in_array(
            $ownerRecord->type,
            [
                StationType::ENTERPRISE_TCH->value,
            ]
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('model')
                    ->label('Model')
                    ->required()
                    ->maxLength(255),

                Select::make('type')
                    ->label('Locomotive Turi')
                    ->options(LocomotiveType::options())
                    ->required(),

                Textarea::make('description')
                    ->label('Tavsif')
                    ->rows(4)
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]);
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('Barchasi'),
        ];

        foreach (LocomotiveType::cases() as $type) {
            $tabs[$type->name] = Tab::make($type->label())
                ->modifyQueryUsing(fn ($query) =>
                    $query->where('type', $type->value)
                );
        }

        return $tabs;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('model')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('model')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Seriya')
                    ->formatStateUsing(fn (string $state) =>
                        LocomotiveType::tryFrom($state)?->value ?? $state
                    )
                    ->searchable(),
                    
                TextColumn::make('description')
                    ->label('Tavsif')
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label('Yangi Lokomotiv Yaratish'),
            ])
            ->recordActions([
                EditAction::make()->button()->label('Tahrirlash'),
                DeleteAction::make()->button()->label('O\'chirish'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
