<?php

use Codedor\TranslatableStrings\ExportToLang as TranslatableStringsExportToLang;
use Codedor\TranslatableStrings\Jobs\ExportToLang;
use Mockery\MockInterface;

it('can export for the given scope', function () {
    $this->instance(
        TranslatableStringsExportToLang::class,
        Mockery::mock(TranslatableStringsExportToLang::class, function (MockInterface $mock) {
            $mock->shouldReceive('export')->once()->with('scope');
        })
    );

    $job = new ExportToLang('scope');

    $job->handle();
});

it('can export all scopes', function () {
    $this->instance(
        TranslatableStringsExportToLang::class,
        Mockery::mock(TranslatableStringsExportToLang::class, function (MockInterface $mock) {
            $mock->shouldReceive('exportAll')->once();
        })
    );

    $job = new ExportToLang;

    $job->handle();
});
