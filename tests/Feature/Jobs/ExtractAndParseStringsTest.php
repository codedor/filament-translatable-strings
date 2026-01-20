<?php

use Mockery\MockInterface;
use Wotz\TranslatableStrings\ExtractTranslatableStrings;
use Wotz\TranslatableStrings\Jobs\ExtractAndParseStrings;

it('can dispatch extract and parse', function () {
    $this->instance(
        ExtractTranslatableStrings::class,
        Mockery::mock(ExtractTranslatableStrings::class, function (MockInterface $mock) {
            $mock->shouldReceive('find->importValidationStrings->save')->once();
        })
    );

    (new ExtractAndParseStrings)->handle();
});
