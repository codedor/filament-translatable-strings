<?php

namespace Codedor\TranslatableStrings\Filament\Resources;

use Codedor\TranslatableStrings\Filament\Resources\TranslatableStringResource\Pages;
use Codedor\TranslatableStrings\Filament\Resources\TranslatableStringResource\Pages\ListTranslatableStrings;
use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;
use Codedor\TranslatableStrings\Models\TranslatableString;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TranslatableStringResource extends Resource
{
    use Translatable;

    protected static ?string $model = TranslatableString::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('scope'),
                TextInput::make('name'),
                MarkdownEditor::make('value')->hidden(fn (?Model $record) => ! $record?->is_html),
                TextInput::make('value')->hidden(fn (?Model $record) => $record?->is_html),
                Checkbox::make('is_html')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('clean_scope')->label('Scope')->sortable(['scope'])->searchable(['scope']),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('key')->hidden()->searchable(),
                // TextColumn::make('value')->sortable(),
                TextInputColumn::make('value')
                    ->rules(['required', 'max:255'])
                    ->updateStateUsing(function (ListTranslatableStrings $livewire, $state, TranslatableString $record) {
                        $record->setTranslation('value', $livewire->getActiveTableLocale(), $state);
                        $record->save();

                        return $state;
                    })
                    ->disabled(fn (TranslatableString $record) => $record?->is_html),
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

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::byOneEmptyValue()->count() . ' ' . __('empty');
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }
}
