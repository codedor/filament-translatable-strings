<?php

namespace Codedor\TranslatableStrings\Console\Commands;

use Codedor\TranslatableStrings\Jobs\ExtractAndParseStrings;
use Illuminate\Console\Command;

class ExtractAndParseTranslatableStrings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translatable-strings:extract-and-parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract and parse translatable strings from your code';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        ExtractAndParseStrings::dispatch();
    }
}
