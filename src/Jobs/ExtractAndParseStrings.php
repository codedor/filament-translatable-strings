<?php

namespace Wotz\TranslatableStrings\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Wotz\TranslatableStrings\ExtractTranslatableStrings;

class ExtractAndParseStrings implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct() {}

    public function handle()
    {
        app(ExtractTranslatableStrings::class)
            ->find(base_path())
            ->importValidationStrings()
            ->save();
    }
}
