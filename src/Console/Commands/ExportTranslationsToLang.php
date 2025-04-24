<?php

namespace Codedor\TranslatableStrings\Console\Commands;

use Codedor\TranslatableStrings\ExportToLang;
use Illuminate\Console\Command;

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
