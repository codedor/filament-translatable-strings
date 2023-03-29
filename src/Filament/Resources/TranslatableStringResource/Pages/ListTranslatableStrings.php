<?php

namespace Codedor\TranslatableStrings\Filament\Resources\TranslatableStringResource\Pages;

use Codedor\TranslatableStrings\Exports\TranslatableStringsExport;
use Codedor\TranslatableStrings\Filament\Resources\TranslatableStringResource;
use Codedor\TranslatableStrings\Imports\TranslatableStringsImport;
use Codedor\TranslatableStrings\Jobs\ExtractAndParseStrings;
use Codedor\TranslatableStrings\Models\TranslatableString;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ListTranslatableStrings extends ListRecords
{
    use ListRecords\Concerns\Translatable;

    protected $listeners = ['refreshTable' => '$refresh'];

    protected static string $resource = TranslatableStringResource::class;

    protected function getActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\ActionGroup::make([
                Actions\Action::make('extract_parse')
                    ->label('Extract and Parse')
                    ->icon('heroicon-o-document-search')
                    ->action('extractAndParseStrings')
                    ->visible(fn (): bool => TranslatableStringResource::canDeleteAny()),
                Actions\Action::make('export')
                    ->label('Export all')
                    ->icon('heroicon-o-download')
                    ->action('exportStrings')
                    ->visible(fn (): bool => TranslatableStringResource::canViewAny()),
                Actions\Action::make('import')
                    ->label('Import all')
                    ->icon('heroicon-o-upload')
                    ->action('importStrings')
                    ->visible(fn (): bool => TranslatableStringResource::canCreate())
                    ->form([
                        FileUpload::make('file')
                            ->disk('local'),
                        Checkbox::make('overwrite')
                            ->label('Overwrite existing strings'),
                    ]),
            ]),
            // export
            //import -> upload + overwrite checkbox
        ];
    }

    public function isTableSearchable(): bool
    {
        return true;
    }

    public function extractAndParseStrings(): void
    {
        ExtractAndParseStrings::dispatch();

        // send notification that the job has been dispatched
        Notification::make()
            ->title('Extract and Parse strings dispatched')
            ->success()
            ->send();
    }

    public function exportStrings()
    {
        return (new TranslatableStringsExport)->download(
            Str::slug(config('app.name') . '_' . today()->toDateString(), '_') . '.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    public function importStrings(array $data): void
    {
        try {
            if ($data['overwrite']) {
                TranslatableString::truncate();
            }

            Excel::import(
                new TranslatableStringsImport,
                new UploadedFile(Storage::disk('local')->path($data['file']), $data['file'])
            );

            $this->emit('refreshTable');

            Notification::make()
                ->title(__('Import was successful'))
                ->success()
                ->send();
        } catch (\Throwable $th) {
            Notification::make()
                ->title(__('Something went wrong during the import'))
                ->body($th->getMessage())
                ->danger()
                ->send();
        }
    }
}
