<?php

use Codedor\TranslatableStrings\ExtractTranslatableStrings;
use Codedor\TranslatableStrings\Jobs\ExtractAndParseStrings;
use Mockery\MockInterface;

it('can dispatch extract and parse', function () {
    $this->instance(
        ExtractTranslatableStrings::class,
        Mockery::mock(ExtractTranslatableStrings::class, function (MockInterface $mock) {
            $mock->shouldReceive('find->importValidationStrings->save')->once();
        })
    );

    (new ExtractAndParseStrings())->handle();
});
