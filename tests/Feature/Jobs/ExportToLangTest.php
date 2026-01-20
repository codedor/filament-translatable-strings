<?php

use Illuminate\Queue\Middleware\Skip;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Wotz\TranslatableStrings\ExportToLang as TranslatableStringsExportToLang;
use Wotz\TranslatableStrings\Jobs\ExportToLang;

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

it('can skip export', function () {
    config()->set('filament-translatable-strings.skip_export_to_lang', true);

    Queue::fake();

    $this->instance(
        TranslatableStringsExportToLang::class,
        Mockery::mock(TranslatableStringsExportToLang::class, function (MockInterface $mock) {
            $mock->shouldReceive('export')->never();
            $mock->shouldReceive('exportAll')->never();
        })
    );

    ExportToLang::dispatch();

    Queue::assertPushed(ExportToLang::class, function ($job) {
        $middleware = $job->middleware();

        expect($middleware)->toHaveCount(1);
        expect($middleware[0])->toBeInstanceOf(Skip::class);

        return true;
    });
});
