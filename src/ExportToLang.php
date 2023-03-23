<?php

namespace Codedor\TranslatableStrings;

use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;
use Codedor\TranslatableStrings\Models\TranslatableString;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ExportToLang
{
    public function __construct(
        protected Filesystem $files
    ) {
    }

    public function exportAll()
    {
        TranslatableString::distinct()
            ->get('scope')
            ->each(fn ($scope) => $this->export($scope->scope));
    }

    public function export(string $scope)
    {
        $basePath = lang_path();

        if ($scope !== ExtractTranslatableStrings::JSON_GROUP) {
            $vendor = false;
            if (Str::startsWith($scope, 'vendor')) {
                $vendor = true;
            }

            $translations = $this->mapTranslatableStringsForScope($scope);

            foreach ($translations as $locale => $strings) {
                $filename = $scope;
                $localePath = $locale . '/' . $filename;

                if ($vendor) {
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
                        $tempPath = rtrim($path . '/' . $subfolderLevel, '/');
                    } else {
                        $tempPath = rtrim($basePath . '/' . $subfolderLevel, '/');
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
                dump($path);
                $output = "<?php\n\nreturn " . var_export($strings, true) . ";" . \PHP_EOL;

                $this->files->put($path, $output);
            }
        } else {
            $translations = $this->mapTranslatableStringsForScope($scope);

            foreach ($translations as $locale => $strings) {
                $path = $basePath . '/' . $locale . '.json';
                $output = json_encode(
                    $strings,
                    \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE
                );
                $this->files->put($path, $output);
            }
        }
    }

    public function mapTranslatableStringsForScope(string $scope): array
    {
        $translations = [];
        TranslatableString::whereScope($scope)
            ->get()
            ->each(function ($translatableString) use (&$translations) {
                LocaleCollection::each(function (Locale $locale) use ($translatableString, &$translations) {
                    $translations[$locale->locale()][$translatableString->key] ??= $translatableString->getTranslation('value', $locale->locale(), false);
                });
            });

        return $translations;
    }
}
