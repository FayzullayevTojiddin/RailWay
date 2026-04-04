<?php

namespace App\Filament\Resources\Stations\RelationManagers;

use Filament\Actions\Action;
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
use App\Imports\MikrosxemaImport;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;

class MikrosxemalarRelationManager extends RelationManager
{
    protected static string $relationship = 'mikrosxemalar';
    protected static ?string $title = 'Kichik mexanizmlar';
    protected static ?string $modelLabel = 'Kichik mexanizm';
    protected static ?string $pluralModelLabel = 'Kichik mexanizmlar';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->type === StationType::ENTERPRISE_PCH->value;
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kichik mexanizm ma\'lumotlari')
                    ->schema([
                        TextInput::make('nomi')
                            ->label('Nomi')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('texnik_holati')
                            ->label('Texnik holati')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(1),

                Section::make('Rasmlar')
                    ->schema([
                        FileUpload::make('rasmlar')
                            ->label('Rasmlar')
                            ->image()
                            ->multiple()
                            ->maxFiles(4)
                            ->directory('mikrosxemalar')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nomi')
            ->columns([
                TextColumn::make('nomi')
                    ->label('Nomi')
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
                CreateAction::make()->label('Yangi kichik mexanizm'),
                Action::make('shablon_yuklab_olish')
                    ->label('Shablon yuklab olish')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->url(route('mikrosxemalar.template')),
                Action::make('import')
                    ->label('Excel Import')
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
                            new MikrosxemaImport($this->getOwnerRecord()->id),
                            $filePath
                        );
                        \Filament\Notifications\Notification::make()
                            ->title('Muvaffaqiyatli import qilindi!')
                            ->success()
                            ->send();
                    }),
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
            ->emptyStateHeading('Kichik mexanizmlar yo\'q')
            ->emptyStateDescription('Yangi kichik mexanizm qo\'shish uchun yuqoridagi tugmani bosing');
    }
}
