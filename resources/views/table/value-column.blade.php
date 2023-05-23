<div class="w-full">
    @php
        $locales = \Codedor\LocaleCollection\Facades\LocaleCollection::map(fn ($locale) => $locale->locale());
        $livewire = $getLivewire();
        $resource = $livewire::getResource();
    @endphp

    <ul>
        @foreach ($locales as $locale)
            <li>
                @php
                    $url = $resource::getUrl('edit', ['record' => $getRecord(), 'locale' => "-{$locale}-tab"]);
                @endphp

                <a href="{{ $url }}" class="flex gap-4 items-center justify-between p-1 rounded-md hover:bg-white transition-colors">
                    <span><strong class="uppercase">{{ $locale }}:</strong> {{ $getRecord()->getTranslation('value', $locale, false) }}</span>

                    <x-heroicon-s-pencil class="h-5 w-5" />
                </a>
            </li>
        @endforeach
    </ul>
</div>
