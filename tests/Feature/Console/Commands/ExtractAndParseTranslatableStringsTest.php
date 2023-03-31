<?php

use Codedor\TranslatableStrings\Jobs\ExtractAndParseStrings;
use Illuminate\Support\Facades\Queue;

it('can run the command', function () {
    Queue::fake();

    $this->artisan('translatable-strings:extract-and-parse')
        ->assertExitCode(0);

    Queue::assertPushed(ExtractAndParseStrings::class);
});
