<?php

use Illuminate\Support\Facades\Queue;
use Wotz\TranslatableStrings\Jobs\ExtractAndParseStrings;

it('can run the command', function () {
    Queue::fake();

    $this->artisan('translatable-strings:extract-and-parse')
        ->assertExitCode(0);

    Queue::assertPushed(ExtractAndParseStrings::class);
});
