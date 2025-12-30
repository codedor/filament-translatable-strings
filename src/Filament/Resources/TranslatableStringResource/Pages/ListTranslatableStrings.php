<?php

namespace Codedor\TranslatableStrings\Filament\Resources\TranslatableStringResource\Pages;

use Codedor\TranslatableStrings\Exports\TranslatableStringsExport;
use Codedor\TranslatableStrings\Filament\Resources\TranslatableStringResource;
use Codedor\TranslatableStrings\Imports\TranslatableStringsImport;
use Codedor\TranslatableStrings\Jobs\ExtractAndParseStrings;
use Codedor\TranslatableStrings\Models\TranslatableString;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ListTranslatableStrings extends ListRecords
{
    // protected $listeners = ['refreshTable' => '$refresh'];

    protected static string $resource = TranslatableStringResource::class;

    protected function getActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('extract_parse')
                    ->label(__('filament-translatable-strings::admin.extract and parse'))
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->action(fn () => $this->extractAndParseStrings())
                    ->visible(fn (): bool => TranslatableStringResource::canDeleteAny()),
                Action::make('export')
                    ->label(__('filament-translatable-strings::admin.export all'))
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->action(fn () => $this->exportStrings())
                    ->visible(fn (): bool => TranslatableStringResource::canViewAny()),
                Action::make('import')
                    ->label(__('filament-translatable-strings::admin.import all'))
                    ->icon('heroicon-o-arrow-up-on-square')
                    ->action(fn (array $data) => $this->importStrings($data))
                    ->visible(fn (): bool => TranslatableStringResource::canCreate())
                    ->schema([
                        FileUpload::make('file')
                            ->label(__('filament-translatable-strings::admin.file'))
                            ->disk('local'),
                        Checkbox::make('overwrite')
                            ->label(__('filament-translatable-strings::admin.overwrite existing strings')),
                    ]),
            ]),
        ];
    }

    public function isTableSearchable(): bool
    {
        return true;
    }

    public function extractAndParseStrings(): void
    {
        ExtractAndParseStrings::dispatch();

        Notification::make()
            ->title(__('filament-translatable-strings::admin.extract and parse strings dispatched'))
            ->success()
            ->send();
    }

    public function exportStrings()
    {
        return app(TranslatableStringsExport::class)->download(
            Str::slug(config('app.name') . '_' . today()->toDateString(), '_') . '.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    public function importStrings(array $data): void
    {
        try {
            if ($data['overwrite']) {
                TranslatableString::query()->update([
                    'value' => '{}',
                ]);
            }

            Excel::import(
                new TranslatableStringsImport,
                new UploadedFile(Storage::disk('local')->path($data['file']), $data['file'])
            );

            $this->dispatch('refreshTable');

            Notification::make()
                ->title(__('filament-translatable-strings::admin.import completed'))
                ->success()
                ->send();
        } catch (\Throwable $th) {
            Notification::make()
                ->title(__('filament-translatable-strings::admin.something went wrong during import'))
                ->body($th->getMessage())
                ->danger()
                ->send();
        }
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
