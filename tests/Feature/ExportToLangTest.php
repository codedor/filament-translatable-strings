<?php

use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;
use Codedor\TranslatableStrings\ExportToLang;
use Codedor\TranslatableStrings\Models\TranslatableString;
use Illuminate\Filesystem\Filesystem;

beforeEach(function () {
    $this->filesystem = app(Filesystem::class);

    $langPath = __DIR__ . '/../fixtures/lang';

    if ($this->filesystem->exists($langPath)) {
        $this->filesystem->deleteDirectory($langPath);
    }

    $this->filesystem->makeDirectory($langPath, 0777, true, true);

    app()->useLangPath($langPath);

    LocaleCollection::add(new Locale('nl'))
        ->add(new Locale('en'));

    app()->setLocale('en');
});

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
        ->toBe([
            'nl' => [
                'no package.underscore' => 'underscore nl',
                'no package.value' => 'value nl',
            ],
            'en' => [
                'no package.underscore' => 'underscore en',
                'no package.value' => 'value en',
            ],
        ]);
});

// it('will export all to the lang directory', function () {
//     TranslatableString::create([
//         'scope' => 'vendor/package/test',
//         'name' => 'underscore',
//         'key' => 'package::test.underscore',
//         'value' => [
//             'en' => 'underscore value'
//         ],
//         'is_html' => false,
//     ]);

//     TranslatableString::create([
//         'scope' => 'no package',
//         'name' => 'underscore',
//         'key' => 'no package.underscore',
//         'value' => [
//             'en' => 'underscore en',
//             'nl' => 'underscore nl'
//         ],
//         'is_html' => false,
//     ]);

//     app(ExportToLang::class)->exportAll();

//     expect(__('package::test.underscore'))->toBe('underscore value');
// });

// it('will export json scope to the lang directory', function () {
//     $export = app(ExportToLang::class)->export();

//     // see if files exists for json scope
// })->skip();

// it('will export no scope to the lang directory', function () {
//     $export = app(ExportToLang::class)->exportAll();

//     // see if files exists for no scope
// })->skip();

// it('will export package scope to the lang directory', function () {
//     $export = app(ExportToLang::class)->exportAll();

//     // see if files exists for package scope
// })->skip();
