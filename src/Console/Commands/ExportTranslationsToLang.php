<?php

namespace Wotz\TranslatableStrings\Console\Commands;

use Illuminate\Console\Command;
use Wotz\TranslatableStrings\ExportToLang;

class ExportTranslationsToLang extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-translations-to-lang';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export the translations from database to lang files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        app(ExportToLang::class)->exportAll();
    }
}
