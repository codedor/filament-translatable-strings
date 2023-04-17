<?php

use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;
use Codedor\LocaleCollection\LocaleCollection as LocaleCollectionLocaleCollection;
use Codedor\TranslatableStrings\ExtractTranslatableStrings;
use Codedor\TranslatableStrings\Models\TranslatableString;
use Illuminate\Translation\Translator;
use Mockery\MockInterface;

beforeEach(function () {
    LocaleCollection::add(new Locale('nl'))
        ->add(new Locale('en'));

    app()->setLocale('en');
});

it('can extract strings from the source code', function () {
    expect(
        app(ExtractTranslatableStrings::class)
            ->find(__DIR__ . '/../Fixtures')
    )
        ->getGroupKeys()->toArray()->toEqualCanonicalizing([
            ['key' => 'test.trans', 'method' => 'trans'],
            ['key' => 'test.trans choice', 'method' => 'trans_choice'],
            ['key' => 'test.lang get', 'method' => 'Lang::get'],
            ['key' => 'test.lang choice', 'method' => 'Lang::choice'],
            ['key' => 'test.underscore html', 'method' => '__html'],
            ['key' => 'test.lang directive', 'method' => '@lang'],
            ['key' => 'test.choice directive', 'method' => '@choice'],
            ['key' => 'test.underscore', 'method' => '__'],
        ])
        ->getStringKeys()->toArray()->toEqualCanonicalizing([
            ['key' => 'trans', 'method' => 'trans'],
            ['key' => 'trans choice', 'method' => 'trans_choice'],
            ['key' => 'lang get', 'method' => 'Lang::get'],
            ['key' => 'lang choice', 'method' => 'Lang::choice'],
            ['key' => 'underscore html', 'method' => '__html'],
            ['key' => 'lang directive', 'method' => '@lang'],
            ['key' => 'choice directive', 'method' => '@choice'],
            ['key' => 'underscore', 'method' => '__'],
        ])
        ->getVendorKeys()->toArray()->toEqualCanonicalizing([
            ['key' => 'package::test.trans', 'method' => 'trans'],
            ['key' => 'package::test.trans choice', 'method' => 'trans_choice'],
            ['key' => 'package::test.lang get', 'method' => 'Lang::get'],
            ['key' => 'package::test.lang choice', 'method' => 'Lang::choice'],
            ['key' => 'package::test.underscore html', 'method' => '__html'],
            ['key' => 'package::test.lang directive', 'method' => '@lang'],
            ['key' => 'package::test.choice directive', 'method' => '@choice'],
            ['key' => 'package::test.underscore', 'method' => '__'],
        ]);
});

it('can import validation strings', function () {
    app(ExtractTranslatableStrings::class)
        ->importValidationStrings()
        ->save();

    $this->assertDatabaseHas(TranslatableString::class, [
        'scope' => 'validation',
        'name' => 'uuid',
        'key' => 'validation.uuid',
        'is_html' => false,
    ]);

    $this->assertDatabaseHas(TranslatableString::class, [
        'scope' => 'validation',
        'name' => 'size.array',
        'key' => 'validation.size.array',
        'is_html' => false,
    ]);
});

it('will save strings to the database', function () {
    app(ExtractTranslatableStrings::class)
        ->find(__DIR__ . '/../Fixtures')
        ->save();

    $this->assertDatabaseCount(TranslatableString::class, 24);

    $this->assertDatabaseHas(TranslatableString::class, [
        'scope' => 'vendor/package/test',
        'name' => 'underscore',
        'key' => 'package::test.underscore',
        'is_html' => false,
    ]);

    $this->assertDatabaseHas(TranslatableString::class, [
        'scope' => 'test',
        'name' => 'lang directive',
        'key' => 'test.lang directive',
        'is_html' => false,
    ]);

    $this->assertDatabaseHas(TranslatableString::class, [
        'scope' => '_json',
        'name' => 'underscore html',
        'key' => 'underscore html',
        'is_html' => true,
    ]);

    $this->assertDatabaseHas(TranslatableString::class, [
        'scope' => '_json',
        'name' => 'lang choice',
        'key' => 'lang choice',
        'is_html' => false,
    ]);
});

it('fall backs to the url locale if locale translation is not found', function () {
    LocaleCollection::swap(new LocaleCollectionLocaleCollection([new Locale('en-GB', null, 'en')]));

    $this->instance(
        'translator',
        Mockery::mock(Translator::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')->once()->with('scope.name', [], 'en-GB', false)->andReturn('scope.name');
            $mock->shouldReceive('get')->once()->with('scope.name', [], 'en', false)->andReturn('en scope');
        })
    );

    app(ExtractTranslatableStrings::class)
        ->missingKey('scope', 'name', 'scope.name', false);

    $this->assertDatabaseCount(TranslatableString::class, 1);
    $this->assertDatabaseHas(TranslatableString::class, [
        'scope' => 'scope',
        'name' => 'name',
        'key' => 'scope.name',
        'is_html' => false,
        'value' => json_encode([
            'en-GB' => 'en scope',
        ]),
    ]);
});
