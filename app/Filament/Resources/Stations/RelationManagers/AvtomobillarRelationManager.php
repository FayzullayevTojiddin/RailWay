<?php

namespace App\Filament\Resources\Stations\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Enums\StationType;
use App\Exports\AvtomobilExport;
use App\Imports\AvtomobilImport;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;

class AvtomobillarRelationManager extends RelationManager
{
    protected static string $relationship = 'avtomobillar';
    protected static ?string $title = 'Avtomobillar';
    protected static ?string $modelLabel = 'Avtomobil';
    protected static ?string $pluralModelLabel = 'Avtomobillar';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        $type = StationType::tryFrom($ownerRecord->type);

        return $type !== null && $type->isEnterprise();
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Avtomobil ma\'lumotlari')
                    ->schema([
                        TextInput::make('rusumi')
                            ->label('Rusumi')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('davlat_raqami')
                            ->label('Davlat raqami')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('ishlab_chiqarilgan_yili')
                            ->label('Ishlab chiqarilgan yili')
                            ->required()
                            ->numeric()
                            ->minValue(1950)
                            ->maxValue(date('Y')),
                        TextInput::make('biriktirilgan_shaxs')
                            ->label('Biriktirilgan shaxs')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('texnik_holati')
                            ->label('Texnik holati')
                            ->maxLength(255),
                    ])
                    ->columns(1),

                Section::make('Rasmlar')
                    ->schema([
                        FileUpload::make('rasmlar')
                            ->label('Avtomobil rasmlari')
                            ->image()
                            ->multiple()
                            ->maxFiles(4)
                            ->directory('avtomobillar')
                            ->imageEditor()
                            ->columnSpanFull(),
                        FileUpload::make('texpassport_old')
                            ->label('Texpassport (old tomoni)')
                            ->image()
                            ->directory('avtomobillar/texpassport')
                            ->imageEditor(),
                        FileUpload::make('texpassport_orqa')
                            ->label('Texpassport (orqa tomoni)')
                            ->image()
                            ->directory('avtomobillar/texpassport')
                            ->imageEditor(),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('rusumi')
            ->columns([
                TextColumn::make('rusumi')
                    ->label('Rusumi')
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('davlat_raqami')
                    ->label('Davlat raqami')
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('ishlab_chiqarilgan_yili')
                    ->label('Ishlab chiqarilgan yili')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('biriktirilgan_shaxs')
                    ->label('Biriktirilgan shaxs')
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('texnik_holati')
                    ->label('Texnik holati')
                    ->searchable()
                    ->alignCenter(),
                ImageColumn::make('rasmlar')
                    ->label('Rasm')
                    ->circular()
                    ->stacked()
                    ->alignCenter()
                    ->limit(2),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label('Yangi avtomobil'),
                ActionGroup::make([
                    Action::make('export')
                        ->label('Export qilish')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('primary')
                        ->action(function () {
                            return Excel::download(
                                new AvtomobilExport($this->getOwnerRecord()->id),
                                'avtomobillar.xlsx'
                            );
                        }),
                    Action::make('shablon_yuklab_olish')
                        ->label('Shablon yuklab olish')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->url(route('avtomobillar.template')),
                    Action::make('import')
                        ->label('Import qilish')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->color('success')
                        ->form([
                            \Filament\Forms\Components\FileUpload::make('file')
                                ->label('Excel fayl tanlang')
                                ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                                ->required(),
                        ])
                        ->action(function (array $data) {
                            $filePath = storage_path('app/private/' . $data['file']);
                            Excel::import(
                                new AvtomobilImport($this->getOwnerRecord()->id),
                                $filePath
                            );
                            \Filament\Notifications\Notification::make()
                                ->title('Muvaffaqiyatli import qilindi!')
                                ->success()
                                ->send();
                        }),
                ])
                    ->label('Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->button(),
            ])
            ->recordActions([
                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Avtomobillar yo\'q')
            ->emptyStateDescription('Yangi avtomobil qo\'shish uchun yuqoridagi tugmani bosing');
    }
}
