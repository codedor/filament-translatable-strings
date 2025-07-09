<?php

namespace Codedor\TranslatableStrings\Filament\Resources;

use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;
use Codedor\TranslatableStrings\Filament\Resources\TranslatableStringResource\Pages;
use Codedor\TranslatableStrings\Models\Builders\TranslatableStringBuilder;
use Codedor\TranslatableStrings\Models\TranslatableString;
use Codedor\TranslatableTabs\Forms\TranslatableTabs;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;

class TranslatableStringResource extends Resource
{
    protected static ?string $model = TranslatableString::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'key';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                TranslatableTabs::make()
                    ->icon(fn (string $locale, \Filament\Schemas\Components\Utilities\Get $get) => 'heroicon-o-' . (
                        empty($get("{$locale}.value")) ? 'x-circle' : 'check-circle'
                    ))
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
                    ->translatableFields(function (TranslatableString $record) {
                        if ($record->is_html) {
                            return [
                                RichEditor::make('value'),
                            ];
                        }

                        return [
                            TextInput::make('value'),
                        ];
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
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
                        true: fn (TranslatableStringBuilder $query) => $query->byFilledInValues(),
                        false: fn (TranslatableStringBuilder $query) => $query->byOneEmptyValue(),
                        blank: fn (TranslatableStringBuilder $query) => $query,
                    ),

                SelectFilter::make('scope')->options(function () {
                    return TranslatableString::groupedScopes()
                        ->toArray();
                })->placeholder('All scopes'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->paginated([25, 50, 100]);
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
        return static::getModel()::query()
            ->byOneEmptyValue()
            ->count() . ' ' . __('empty');
    }
}
