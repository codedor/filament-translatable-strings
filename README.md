# This package manages translatable strings in Filament.

Package to manage the lang files in [Filament](https://filamentphp.com/) with import and export actions and a command to find them in your code.

## Installation

You can install the package via composer:

```bash
composer require wotz/filament-translatable-strings
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
    'skip_export_to_lang' => (bool) env('SKIP_EXPORT_TO_LANG', false),
];
```

Register the plugin and/or Widget in your Panel provider:

```php
public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            \Wotz\TranslatableStrings\TranslatableStringsPlugin::make(),
        ]);
    }
```

In an effort to align with Filament's theming methodology you will need to use a custom theme to use this plugin.

> **Note**
> If you have not set up a custom theme and are using a Panel follow the instructions in the [Filament Docs](https://filamentphp.com/docs/3.x/panels/themes#creating-a-custom-theme) first. The following applies to both the Panels Package and the standalone Forms package.

1. Import the plugin's stylesheet and views into your theme's css file.

```css
@import '../../../../vendor/wotz/filament-translatable-strings/resources/css/plugin.css';
@source '../../../../vendor/wotz/filament-translatable-strings/resources/**/*.blade.php';
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

If you discover any security-related issues, please email info@whoownsthezebra.be instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
