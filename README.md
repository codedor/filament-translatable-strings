# This package manages translatable strings in Filament.

Package to manage the lang files in [Filament](https://filamentphp.com/) with import and export actions and a command to find them in your code.

## Installation

You can install the package via composer:

```bash
composer require codedor/filament-translatable-strings
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-translatable-strings-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-translatable-strings-config"
```

This is the contents of the published config file:

```php
return [
    'trans_functions' => [
        '__',
        'trans',
        'trans_choice',
        'Lang::get',
        'Lang::choice',
        '@lang',
        '@choice',
    ],
    'html_trans_functions' => [
        '__html',
    ],
    'exclude_folders' => [
        'storage',
        'node_modules',
        'database',
        'lang',
        'vendor/symfony',
        'tests',
    ],
];
```

## Usage

```bash
php artisan translatable-strings:extract-and-parse
```

## Documentation

For the full documentation, check [here](./docs/index.md).

## Testing

```bash
vendor/bin/pest
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Upgrading

Please see [UPGRADING](UPGRADING.md) for more information on how to upgrade to a new version.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you discover any security-related issues, please email info@codedor.be instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
