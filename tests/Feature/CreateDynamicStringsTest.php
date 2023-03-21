<?php

use Codedor\TranslatableStrings\Models\TranslatableString;

// beforeEach(function () {
// })->skip('Need to find way to intercept missing keys without overriding the translator file');

// it('can add scoped translatable strings on the fly', function () {
//     __('non.existing');

//     $this->assertDatabaseHas(TranslatableString::class, [
//         'name' => 'existing',
//         'scope' => 'non',
//         'key' => 'non.existing',
//     ]);
// });

// it('can add translatable strings on the fly', function () {
//     __('non-existing');

//     $this->assertDatabaseHas(TranslatableString::class, [
//         'name' => 'non-existing',
//         'scope' => null,
//         'key' => 'non-existing',
//     ]);
// });

// it('can add translatable strings from packages on the fly', function () {
//     __('package::non.existing');

//     $this->assertDatabaseHas(TranslatableString::class, [
//         'name' => 'existing',
//         'scope' => 'package - non',
//         'key' => 'package::non.existing',
//     ]);
// });

// it('can add translatable html strings on the fly', function () {
//     __html('non.existing html');

//     $this->assertDatabaseHas(TranslatableString::class, [
//         'name' => 'existing html',
//         'scope' => 'non',
//         'key' => 'non.existing html',
//     ]);
// });
