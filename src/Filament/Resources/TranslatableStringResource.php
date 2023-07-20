<?php

namespace Codedor\TranslatableStrings\Filament\Resources;

use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;
use Codedor\TranslatableStrings\Filament\Resources\TranslatableStringResource\Pages;
use Codedor\TranslatableStrings\Models\TranslatableString;
use Codedor\TranslatableTabs\Forms\TranslatableTabs;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TranslatableStringResource extends Resource
{
    protected static ?string $model = TranslatableString::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'key';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TranslatableTabs::make('translations')
                    ->icon(fn (string $locale, Get $get) => 'heroicon-o-signal' . (empty($get("{$locale}.value")) ? '-slash' : ''))
                    ->iconColor(fn (string $locale, Get $get) => empty($get("{$locale}.value")) ? 'danger' : 'success')
                    ->defaultFields([
                        TextInput::make('scope')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('name')
                            ->disabled()
                            ->dehydrated(false),
                        Checkbox::make('is_html')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->translatableFields([
                        MarkdownEditor::make('value')
                            ->hidden(fn (?TranslatableString $record) => ! $record?->is_html),
                        TextInput::make('value')
                            ->hidden(fn (?TranslatableString $record) => $record?->is_html),
                    ])
                    ->columnSpan(['lg' => 2]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $localeColumns = LocaleCollection::map(
            fn (Locale $locale) => TextColumn::make($locale->locale())
                ->formatStateUsing(fn (TranslatableString $record): string => $locale->locale() . ': ' . $record->getTranslation('value', $locale->locale()))
        );

        return $table
            ->columns([
                Split::make([
                    TextColumn::make('created_at')->dateTime()->sortable(),
                    TextColumn::make('clean_scope')->label('Scope')->sortable(['scope'])->searchable(['scope']),
                    TextColumn::make('name')->sortable()->searchable(),
                    TextColumn::make('key')->hidden()->searchable(),
                ]),
                Panel::make([
                    Stack::make([
                        ViewColumn::make('value')->view('filament-translatable-strings::table.value-column'),
                    ]),
                ])->collapsible(),
            ])
            ->filters([
                TernaryFilter::make('filled_in')
                    ->placeholder('All records')
                    ->trueLabel('Only filled in records')
                    ->falseLabel('Only not filled in records')
                    ->queries(
                        true: fn (Builder $query) => $query->byFilledInValues(),
                        false: fn (Builder $query) => $query->byOneEmptyValue(),
                        blank: fn (Builder $query) => $query,
                    ),

                SelectFilter::make('scope')->options(function () {
                    return TranslatableString::groupedScopes()
                        ->toArray();
                })->placeholder('All scopes'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTranslatableStrings::route('/'),
            'edit' => Pages\EditTranslatableString::route('/{record}/edit'),
        ];
    }

    public static function getTranslatableLocales(): array
    {
        return LocaleCollection::map(fn (Locale $locale) => $locale->locale())->toArray();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::byOneEmptyValue()->count() . ' ' . __('empty');
    }
}
