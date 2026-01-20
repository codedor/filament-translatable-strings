# This package manages translatable strings in Filament

## Introduction

The Translatable Strings package is a package that helps you manage and translate the strings in your Laravel applications. With this package, you can easily import and export your translatable strings to and from Excel. This makes it easy to collaborate with other team members and translation agencies.

In addition to import and export functionality, the package also provides an extract and parse command. This command scans your source code for translatable strings and adds them to the database.

It is also fully integrated with the Filament admin panel, making it easy to manage your translations from within your application's dashboard.

## Installation

First, install this package via the Composer package manager:

```bash
composer require wotz/filament-translatable-strings
```

After that you can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-translatable-strings-migrations"
php artisan migrate
```

You can also publish the config file with:

```bash
php artisan vendor:publish --tag="filament-translatable-strings-config"
```

See the [Configuration](#configuration) section for more information.

Behind the scenes we use the [Locale Collection](https://github.com/wotzebra/laravel-locale-collection) package. So do not forget to define locales in your provider.

```php
use Wotz\LocaleCollection\Facades\LocaleCollection;
use Wotz\LocaleCollection\Locale;

LocaleCollection::add(new Locale('nl'))
    ->add(new Locale('en'));
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

## Commands

```bash
php artisan translatable-strings:extract-and-parse
```

This command can be used to extract and parse the i18n helpers from the source code and save them to the `translatable_strings` table, so they can be managed in Filament.

This command can be added to your deploy script to extract the changes automatically.

## Configuration

The config has 4 keys: `trans_functions`, `html_trans_functions`, `exclude_folders` and `skip_export_to_lang`.

### trans_functions

These are the functions we will use in the regex to parse them from the source code.

Default:

```php
'trans_functions' => [
    '__',
    'trans',
    'trans_choice',
    'Lang::get',
    'Lang::choice',
    '@lang',
    '@choice',
],
```

### html_trans_functions

These are the functions we will use in the regex to parse them from the source code and will show a WYSIWIG editor in Filament.

Default:

```php
'html_trans_functions' => [
    '__html',
],
```

### exclude_folders

These are the folders to exclude when extracting from the source code.

Default:

```php
'exclude_folders' => [
    'storage',
    'node_modules',
    'database',
    'lang',
    'vendor/symfony',
    'tests',
],
```

### skip_export_to_lang

This setting allows you to disable the automatic export of translations to the language files. This can be useful in environments where you don't want the translations to be written to disk (e.g., production environments with read-only filesystems).

Default:

```php
'skip_export_to_lang' => (bool) env('SKIP_EXPORT_TO_LANG', false),
```

When set to `true`, the `ExportToLang` job will be skipped and translations will not be written to the `lang/` directory.

## Filament

In Filament you will see a "Translatable Strings" resource which is automatically registered via our provider.

Translatable Strings wrapped in `__html()` will have the `is_html` checked and a WYSIWIG editor will be shown instead of a simple text input.

Non-html strings will also have an editable column on the index page. To switch languages we use the [Spatie Translatable](https://filamentphp.com/docs/2.x/spatie-laravel-translatable-plugin/installation) package.

When a string is saved the `ExportToLang` job is dispatched, so the lang folder can be updated with the new translations.

### Export action

On the index page you can also take an export of the translatable strings, which will download an Excel file with every scope in a separate tab. So it's easily shared with a translation agency.

### Import action

To import the file, you can run the import action and upload the file.
The `overwrite` checkbox, when checked will truncate the current values, so what is defined in the excel will also be shown. After the import is done the `ExportToLang` job is also dispatched.

### Extract and parse action

This will scan your source code and import the i18n helpers to the database.
