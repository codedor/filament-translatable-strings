<?php

namespace Codedor\TranslatableStrings\Jobs;

use Codedor\TranslatableStrings\ExtractTranslatableStrings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class ExtractAndParseStrings implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct()
    {
    }

    public function handle()
    {
        app(ExtractTranslatableStrings::class)
            ->find(base_path())
            ->importValidationStrings()
            ->save();
    }
}
