<div>
    @php
        $locales = \Codedor\LocaleCollection\Facades\LocaleCollection::map(fn ($locale) => $locale->locale());
        $livewire = $getLivewire();
        $resource = $livewire::getResource();
    @endphp

    @foreach ($locales as $locale)
        {{ $locale }}: {{ $getRecord()->getTranslation('value', $locale, false) }}

        @php
            $url = $resource::getUrl('edit', ['record' => $getRecord(), 'locale' => "-{$locale}-tab"]);

        @endphp

        <a href="{{ $url }}">
            <x-heroicon-s-pencil class="h-6 w-6 text-red-600" />
        </a>
    @endforeach
</div>
