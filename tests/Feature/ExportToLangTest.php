<?php

use Illuminate\Filesystem\Filesystem;
use Wotz\LocaleCollection\Facades\LocaleCollection;
use Wotz\LocaleCollection\Locale;
use Wotz\TranslatableStrings\ExportToLang;
use Wotz\TranslatableStrings\ExtractTranslatableStrings;
use Wotz\TranslatableStrings\Models\TranslatableString;

beforeEach(function () {
    $this->filesystem = app(Filesystem::class);

    $this->langPath = __DIR__ . '/../lang';

    if ($this->filesystem->exists($this->langPath)) {
        $this->filesystem->deleteDirectory($this->langPath);
    }

    $this->filesystem->makeDirectory($this->langPath, 0777, true, true);

    app()->useLangPath($this->langPath);

    LocaleCollection::add(new Locale('nl'))
        ->add(new Locale('en'));

    app()->setLocale('en');
});

// afterEach(function () {
//     if ($this->filesystem->exists($this->langPath)) {
//         $this->filesystem->deleteDirectory($this->langPath);
//     }
// });

it('can map translatable strings for a given scope', function () {
    TranslatableString::create([
        'scope' => 'vendor/package/test',
        'name' => 'underscore',
        'key' => 'package::test.underscore',
        'value' => [
            'en' => 'underscore value',
        ],
        'is_html' => false,
    ]);

    TranslatableString::create([
        'scope' => 'no package',
        'name' => 'underscore',
        'key' => 'no package.underscore',
        'value' => [
            'en' => 'underscore en',
            'nl' => 'underscore nl',
        ],
        'is_html' => false,
    ]);

    TranslatableString::create([
        'scope' => 'no package',
        'name' => 'value',
        'key' => 'no package.value',
        'value' => [
            'en' => 'value en',
            'nl' => 'value nl',
        ],
        'is_html' => false,
    ]);

    expect(app(ExportToLang::class)->mapTranslatableStringsForScope('no package'))
        ->toArray()->toBe([
            'nl' => [
                'underscore' => 'underscore nl',
                'value' => 'value nl',
            ],
            'en' => [
                'underscore' => 'underscore en',
                'value' => 'value en',
            ],
        ]);
});

it('will export all to the lang directory', function () {
    TranslatableString::create([
        'scope' => 'vendor/package/test',
        'name' => 'underscore',
        'key' => 'package::test.underscore',
        'value' => [
            'en' => 'underscore value',
        ],
        'is_html' => false,
    ]);

    TranslatableString::create([
        'scope' => 'no package',
        'name' => 'underscore',
        'key' => 'no package.underscore',
        'value' => [
            'en' => 'underscore en',
            'nl' => 'underscore nl',
        ],
        'is_html' => false,
    ]);

    app('translator')->addNamespace('package', 'path-to-package');
    app(ExportToLang::class)->exportAll();

    expect(__('package::test.underscore'))->toBe('underscore value');
    expect(__('no package.underscore'))->toBe('underscore en');
});

it('will export json scope to the lang directory', function () {
    TranslatableString::create([
        'scope' => ExtractTranslatableStrings::JSON_GROUP,
        'name' => 'json',
        'key' => 'json',
        'value' => [
            'en' => 'json en',
            'nl' => 'json nl',
        ],
        'is_html' => false,
    ]);

    app(ExportToLang::class)->export(ExtractTranslatableStrings::JSON_GROUP);

    expect(__('json'))->toBe('json en');
});

it('will not export empty values to the lang directory', function () {
    TranslatableString::create([
        'scope' => 'test',
        'name' => 'underscore',
        'key' => 'test.underscore',
        'value' => [
            'en' => '',
        ],
        'is_html' => false,
    ]);

    app(ExportToLang::class)->exportAll();

    expect(__('test.underscore'))->toBe('test.underscore');
});
