<?php

namespace Codedor\TranslatableStrings;

use Codedor\TranslatableStrings\Models\TranslatableString;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class ExtractTranslatableStrings
{
    const JSON_GROUP = '_json';

    protected Collection $groupKeys;
    protected Collection $stringKeys;
    protected Collection $vendorKeys;

    public function __construct(
        protected Filesystem $files
    ) {
        $this->groupKeys = new Collection();
        $this->stringKeys = new Collection();
        $this->vendorKeys = new Collection();
    }

    public function find(string $path): self
    {
        // Find all PHP files in the app folder, except for storage
        $finder = new Finder();
        $finder
            ->in($path)
            ->exclude(config('translations.exclude_folders', []))
            ->name('*.php')
            ->name('*.vue')
            ->files();

        foreach ($finder as $file) {
            // Search the current file for the pattern
            if (preg_match_all("/{$this->getVendorPattern()}/siU", $file->getContents(), $matches)) {
                // Get all matches
                foreach ($matches[2] as $index => $key) {
                    $this->vendorKeys->add([
                        'key' => $key,
                        'method' => $matches[1][$index] ?? null,
                    ]);
                }
            }

            if (preg_match_all("/{$this->getGroupPattern()}/siU", $file->getContents(), $matches)) {
                // Get all matches
                foreach ($matches[2] as $index => $key) {
                    $this->groupKeys->add([
                        'key' => $key,
                        'method' => $matches[1][$index] ?? null,
                    ]);
                }
            }

            if (preg_match_all("/{$this->getStringPattern()}/siU", $file->getContents(), $matches)) {
                foreach ($matches['string'] as $index => $key) {
                    if (preg_match("/(^[a-zA-Z0-9_-]+([.][^\1)\ ]+)+$)/siU", $key)) {
                        // group{.group}.key format, already in $groupKeys but also matched here
                        // do nothing, it has to be treated as a group
                        continue;
                    }

                    if ((! (Str::contains($key, '::') && Str::contains($key, '.')) || Str::contains($key, ' '))
                        && $this->groupKeys->doesntContain('key', $key) && $this->vendorKeys->doesntContain('key', $key)
                    ) {
                        $this->stringKeys->add([
                            'key' => $key,
                            'method' => $matches[1][$index] ?? null,
                        ]);
                    }
                }
            }
        }

        // Translatable::getTranslatableRouteParts()->each(
        //     function ($item) use (&$groupKeys) {
        //         if ($item) {
        //             $groupKeys[] = 'routes.' . $item;
        //         }
        //     }
        // );

        return $this;
    }

    public function importValidationStrings(): self
    {
        collect(__('validation'))->dot()->keys()->each(function ($key) {
            $this->missingKey(
                'validation',
                $key,
                "validation.{$key}",
                false,
                $this->getValue("validation.{$key}"),
            );
        });

        return $this;
    }

    public function save(): self
    {
        $this
            ->saveVendorKeys()
            ->saveGroupKeys()
            ->saveStringKeys();

        return $this;
    }

    public function missingKey(string $scope, string $name, string $key, bool $isHtml = false, $values = [])
    {
        TranslatableString::flushEventListeners();

        TranslatableString::updateOrCreate([
            'scope' => $scope,
            'name' => $name,
        ], [
            'key' => $key,
            'is_html' => $isHtml,
            'value' => $values,
        ]);
    }

    public function getAllFunctions(): Collection
    {
        return $this->getTransFunctions()
            ->merge($this->getHtmlTransFunctions());
    }

    public function getTransFunctions(): Collection
    {
        return collect(config('filament-translatable-strings.trans_functions', []));
    }

    public function getHtmlTransFunctions(): Collection
    {
        return collect(config('filament-translatable-strings.html_trans_functions', []));
    }

    public function getVendorPattern()
    {
        // See https://regex101.com/r/WEJqdL/6
        return '[^\w|>]' . // Must not have an alphanum or _ or > before real method
        '(' . $this->getAllFunctions()->implode('|') . ')' . // Must start with one of the functions
        '\(' . // Match opening parenthesis
        '[\'\"]' . // Match " or '
        '(' . // Start a new group to match:
        '[a-zA-Z0-9_-]+' . // Must start with group
        '([\:\:](?! )[^\1)]+)+' . // Be followed by one or more items/keys
        ')' . // Close group
        '[\'\"]' . // Closing quote
        '[\),]'; // Close parentheses or new parameter
    }

    public function getGroupPattern()
    {
        // See https://regex101.com/r/WEJqdL/6
        return '[^\w|>]' . // Must not have an alphanum or _ or > before real method
        '(' . $this->getAllFunctions()->implode('|') . ')' . // Must start with one of the functions
        '\(' . // Match opening parenthesis
        '[\'\"]' . // Match " or '
        '(' . // Start a new group to match:
        '[a-zA-Z0-9_-]+' . // Must start with group
        '([.](?! )[^\1)]+)+' . // Be followed by one or more items/keys
        ')' . // Close group
        '[\'\"]' . // Closing quote
        '[\),]'; // Close parentheses or new parameter
    }

    public function getStringPattern()
    {
        return '[^\w]' . // Must not have an alphanum before real method
        '(' . $this->getAllFunctions()->implode('|') . ')' . // Must start with one of the functions
        '\(' . // Match opening parenthesis
        '(?P<quote>[\'"])' . // Match " or ' and store in {quote}
        '(?P<string>(?:\\\k{quote}|(?!\k{quote}).)*)' . // Match any string that can be {quote} escaped
        '\k{quote}' . // Match " or ' previously matched
        '[\),]'; // Close parentheses or new parameter
    }

    public function exportAll()
    {
        TranslatableString::distinct()
            ->get('scope')
            ->each(function ($scope) {
                $this->export($scope->scope);
            });
    }

    public function export(string $scope)
    {
        $basePath = app('path.lang');

        if ($scope !== self::JSON_GROUP) {
            if ($this->inExcludedScopes($scope)) {
                return false;
            }

            $vendor = false;
            if (Str::startsWith($scope, 'vendor')) {
                $vendor = true;
            }

            $localizationStrings = TranslatableString::where('scope', $scope)
                ->get();

            $translations = [];
            foreach ($localizationStrings as $localizationString) {
                foreach ($this->getLocales() as $locale) {
                    if (in_array($locale, config('translations.exclude_langs', [])) !== false) {
                        continue;
                    }

                    if (! isset($translations[$locale])) {
                        $translations[$locale] = [];
                    }

                    $translation = $localizationString->translations->firstWhere('locale', $locale);

                    if ($translation && $translation->value) {
                        $translations[$locale][$localizationString->name] = $translation->value;
                    }
                }
            }

            foreach ($translations as $locale => $strings) {
                $path = app('path.lang');
                $filename = $scope;
                $localePath = $locale . '/' . $filename;

                if ($vendor) {
                    // $path = $basePath . '/' . $scope . '/' . $locale;
                    // $localePath = Str::after($scope, '/');

                    $groupParts = explode('/', $scope);
                    $filename = $groupParts[2];
                    $path = sprintf('%s/%s/%s', $basePath, $groupParts[0], $groupParts[1]);
                    $localePath = $groupParts[0] . '/' . $groupParts[1] . '/' . $locale . '/' . $filename;
                }

                $subfolders = explode('/', $localePath);
                array_pop($subfolders);
                $subfolderLevel = '';

                foreach ($subfolders as $subfolder) {
                    $subfolderLevel = $subfolderLevel . $subfolder . '/';

                    if ($vendor) {
                        $tempPath = rtrim($basePath . '/' . $subfolderLevel, '/');
                    } else {
                        $tempPath = rtrim($path . '/' . $subfolderLevel, '/');
                    }

                    if (! is_dir($tempPath)) {
                        mkdir($tempPath, 0777, true);
                    }
                }

                if ($vendor) {
                    $path = $path . '/' . $locale . '/' . $filename . '.php';
                } else {
                    $path = $path . '/' . $locale . '/' . $filename . '.php';
                }

                $output = "<?php\n\nreturn " . var_export($strings, true) . ";" . \PHP_EOL;

                $this->files->put($path, $output);
            }
        } else {
            $localizationStrings = TranslatableString::where('scope', $scope)
                ->with('translations')
                ->get();

            $translations = [];
            foreach ($localizationStrings as $localizationString) {
                foreach ($this->getLocales() as $locale) {
                    if (! isset($translations[$locale])) {
                        $translations[$locale] = [];
                    }

                    $translation = $localizationString->translations->firstWhere('locale', $locale);

                    if ($translation && $translation->value) {
                        $translations[$locale][$localizationString->name] = $translation->value;
                    }
                }
            }

            foreach ($translations as $locale => $strings) {
                $path = app('path.lang') . '/' . $locale . '.json';
                $output = json_encode(
                    $strings,
                    \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE
                );
                $this->files->put($path, $output);
            }
        }
    }

    public function getLocales(): array
    {
        return config('translatable.locales', []);
    }

    protected function getValue(string $key): array
    {
        $value = [];

        foreach ($this->getLocales() as $locale) {
            $translation = app('translator')->get($key, [], $locale, false);

            if ($key !== $translation && ! is_array($translation)) {
                $value[$locale] = $translation;
            }
        }

        return $value;
    }

    protected function saveVendorKeys(): self
    {
        $this->getVendorKeys()->each(function ($vendorKey) {
            list($scope, $name) = explode('.', $vendorKey['key'], 2);

            $this->missingKey(
                'vendor/' . str_replace('::', '/', $scope),
                $name,
                $vendorKey['key'],
                $this->isHtml($vendorKey['method'], $scope),
                $this->getValue($vendorKey['key'])
            );
        });

        return $this;
    }

    protected function saveGroupKeys(): self
    {
        $this->getGroupKeys()->each(function ($groupKey) {
            list($scope, $name) = explode('.', $groupKey['key'], 2);

            $this->missingKey(
                $scope,
                $name,
                $groupKey['key'],
                $this->isHtml($groupKey['method'], $scope),
                $this->getValue($groupKey['key'])
            );
        });

        return $this;
    }

    protected function saveStringKeys(): self
    {
        $this->getStringKeys()->each(function ($stringKey) {
            $this->missingKey(
                self::JSON_GROUP,
                $stringKey['key'],
                $stringKey['key'],
                $this->isHtml($stringKey['method']),
                $this->getValue($stringKey['key'])
            );
        });

        return $this;
    }

    public function getGroupKeys(): Collection
    {
        return $this->groupKeys->unique();
    }

    public function getStringKeys(): Collection
    {
        return $this->stringKeys->unique();
    }

    public function getVendorKeys(): Collection
    {
        return $this->vendorKeys->unique();
    }

    protected function isHtml(string $method, ?string $scope = null): bool
    {
        if ($scope === 'routes') {
            return false;
        }

        return $this->getHtmlTransFunctions()->contains($method);
    }
}
