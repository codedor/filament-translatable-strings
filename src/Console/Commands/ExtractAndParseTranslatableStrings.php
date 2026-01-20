<?php

namespace Wotz\TranslatableStrings\Console\Commands;

use Illuminate\Console\Command;
use Wotz\TranslatableStrings\Jobs\ExtractAndParseStrings;

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
