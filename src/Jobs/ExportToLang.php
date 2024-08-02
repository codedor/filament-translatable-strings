<?php

namespace Codedor\TranslatableStrings\Jobs;

use Codedor\TranslatableStrings\ExportToLang as TranslatableStringsExportToLang;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class ExportToLang implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public ?string $scope = null
    ) {}

    public function handle()
    {
        if ($this->scope) {
            return app(TranslatableStringsExportToLang::class)
                ->export($this->scope);
        }

        app(TranslatableStringsExportToLang::class)
            ->exportAll();
    }
}
