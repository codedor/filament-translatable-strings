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

                if ($vendor) {
                    $groupParts = explode('/', $scope);
                    $filename = $groupParts[2];
                    $path = sprintf('%s/%s/%s', $basePath, $groupParts[0], $groupParts[1]);
                    $localePath = $basePath . '/' . $groupParts[0] . '/' . $groupParts[1] . '/' . $locale . '/' . $filename;
                } else {
                    $localePath = $basePath . '/' . $locale . '/' . $filename;
                }

                if (! $this->files->isDirectory(dirname($localePath))) {
                    $this->files->makeDirectory(dirname($localePath), 0755, true);
                }

                // $stringsToSave = $strings
                $output = "<?php\n\nreturn " . var_export($strings->toArray(), true) . ';' . \PHP_EOL;

                $this->files->put("{$localePath}.php", $output);
            }
        } else {
            $translations = $this->mapTranslatableStringsForScope($scope);

            foreach ($translations as $locale => $strings) {
                $path = $basePath . '/' . $locale . '.json';
                $output = json_encode(
                    $strings->toArray(),
                    \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE
                );
                $this->files->put($path, $output);
            }
        }
    }

    public function mapTranslatableStringsForScope(string $scope): Collection
    {
        $translatableStrings = TranslatableString::whereScope($scope)->get();

        return LocaleCollection::mapToGroups(fn (Locale $locale) => [
            $locale->locale() => $translatableStrings->mapWithKeys(fn ($translatableString) => [
                $translatableString->name => $translatableString->getTranslation('value', $locale->locale(), false),
            ]),
        ])
            ->mapWithKeys(fn ($item, $locale) => [$locale => $item->first()]);
    }
}
