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
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use App\Enums\StationType;
use App\Exports\MikrosxemaExport;
use App\Imports\MikrosxemaImport;
use App\Models\Mikrosxema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
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
                        TextInput::make('biriktirilgan_joyi')
                            ->label('Biriktirilgan joyi')
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
        $stationId = $this->getOwnerRecord()->id;

        $counts = Mikrosxema::query()
            ->where('station_id', $stationId)
            ->selectRaw('nomi, texnik_holati, COUNT(*) as c')
            ->groupBy('nomi', 'texnik_holati')
            ->get()
            ->groupBy('nomi')
            ->map(function ($rows) {
                $soz = (int) ($rows->firstWhere('texnik_holati', 'soz')->c ?? 0);
                $nosoz = (int) ($rows->firstWhere('texnik_holati', 'nosoz')->c ?? 0);
                return ['soz' => $soz, 'nosoz' => $nosoz, 'total' => $soz + $nosoz];
            });

        return $table
            ->recordTitleAttribute('nomi')
            ->groups([
                Group::make('nomi')
                    ->label('Nomi')
                    ->titlePrefixedWithLabel(false)
                    ->getDescriptionFromRecordUsing(function ($record) use ($counts) {
                        $c = $counts[$record->nomi] ?? ['soz' => 0, 'nosoz' => 0, 'total' => 0];
                        $sozPct = $c['total'] > 0 ? round(($c['soz'] / $c['total']) * 100) : 0;
                        $nosozPct = 100 - $sozPct;

                        $wrap = 'display:flex;align-items:center;justify-content:space-between;width:100%;margin-top:10px;padding-right:32px;gap:32px;flex-wrap:wrap';
                        $stats = 'display:inline-flex;align-items:center;gap:10px;flex-wrap:wrap';
                        $pillBase = 'display:inline-flex;align-items:center;gap:8px;padding:5px 12px;border-radius:999px;font-size:12px;line-height:1;border:1px solid';
                        $num = 'font-weight:700;font-size:13px;font-variant-numeric:tabular-nums;letter-spacing:.2px';
                        $lbl = 'text-transform:uppercase;font-size:10px;letter-spacing:.7px;font-weight:600;opacity:.85';

                        $totalPill = $pillBase . ';background:rgba(148,163,184,.10);border-color:rgba(148,163,184,.25);color:inherit';
                        $sozPill = $c['soz'] > 0
                            ? $pillBase . ';background:rgba(34,197,94,.15);border-color:rgba(34,197,94,.40);color:#16a34a'
                            : $pillBase . ';background:rgba(100,116,139,.10);border-color:rgba(100,116,139,.25);color:#6b7280';
                        $nosozPill = $c['nosoz'] > 0
                            ? $pillBase . ';background:rgba(248,113,113,.15);border-color:rgba(248,113,113,.40);color:#dc2626'
                            : $pillBase . ';background:rgba(100,116,139,.10);border-color:rgba(100,116,139,.25);color:#6b7280';

                        $chartBg = $c['nosoz'] > 0 ? 'rgba(248,113,113,.55)' : 'rgba(100,116,139,.3)';
                        $chartFg = '#22c55e';

                        return new HtmlString(
                            '<style>.fi-ta-group-header>div:not(.fi-ta-group-checkbox-ctn){flex:1 1 auto;min-width:0}.fi-ta-group-description{display:block;width:100%}</style>'
                            . '<span style="' . $wrap . '">'
                            . '<span style="' . $stats . '">'
                                . '<span style="' . $totalPill . '"><span style="' . $num . '">' . $c['total'] . '</span><span style="' . $lbl . '">jami</span></span>'
                                . '<span style="' . $sozPill . '"><span style="' . $num . '">' . $c['soz'] . '</span><span style="' . $lbl . '">soz</span></span>'
                                . '<span style="' . $nosozPill . '"><span style="' . $num . '">' . $c['nosoz'] . '</span><span style="' . $lbl . '">nosoz</span></span>'
                            . '</span>'
                            . '<span style="position:relative;width:42px;height:42px;flex-shrink:0;display:inline-block;margin-left:auto">'
                                . '<svg width="42" height="42" viewBox="0 0 42 42" style="display:block">'
                                    . '<circle cx="21" cy="21" r="17" fill="none" stroke="' . $chartBg . '" stroke-width="5"/>'
                                    . ($c['soz'] > 0
                                        ? '<circle cx="21" cy="21" r="17" fill="none" stroke="' . $chartFg . '" stroke-width="5" pathLength="100" stroke-dasharray="' . $sozPct . ' ' . $nosozPct . '" stroke-linecap="round" transform="rotate(-90 21 21)"/>'
                                        : '')
                                . '</svg>'
                                . '<span style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:inherit;font-variant-numeric:tabular-nums">' . $sozPct . '%</span>'
                            . '</span>'
                            . '</span>'
                        );
                    })
                    ->collapsible(),
            ])
            ->defaultGroup('nomi')
            ->collapsedGroupsByDefault()
            ->paginated(false)
            ->columns([
                TextColumn::make('nomi')
                    ->label('Nomi')
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('texnik_holati')
                    ->label('Texnik holati')
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('biriktirilgan_joyi')
                    ->label('Biriktirilgan joyi')
                    ->searchable()
                    ->alignCenter(),
                ImageColumn::make('rasmlar')
                    ->label('Rasm')
                    ->circular()
                    ->stacked()
                    ->alignCenter()
                    ->limit(2),
            ])
            ->headerActions([
                CreateAction::make()->label('Yangi kichik mexanizm'),
                ActionGroup::make([
                    Action::make('export')
                        ->label('Export qilish')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('primary')
                        ->action(function () {
                            return Excel::download(
                                new MikrosxemaExport($this->getOwnerRecord()->id),
                                'kichik_mexanizmlar.xlsx'
                            );
                        }),
                    Action::make('shablon_yuklab_olish')
                        ->label('Shablon yuklab olish')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->url(route('mikrosxemalar.template')),
                    Action::make('import')
                        ->label('Import qilish')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->color('success')
                        ->form([
                            FileUpload::make('file')
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
            ->emptyStateHeading('Kichik mexanizmlar yo\'q')
            ->emptyStateDescription('Yangi kichik mexanizm qo\'shish uchun yuqoridagi tugmani bosing');
    }
}
