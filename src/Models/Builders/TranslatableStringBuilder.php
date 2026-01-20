<?php

namespace Wotz\TranslatableStrings\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Wotz\LocaleCollection\Facades\LocaleCollection;
use Wotz\LocaleCollection\Locale;

class TranslatableStringBuilder extends Builder
{
    public function byOneEmptyValue(): self
    {
        return $this->orWhere(
            fn (self $query) => LocaleCollection::each(
                fn (Locale $locale) => $query->orWhereNull("value->{$locale->locale()}")
            )
        );
    }

    public function byAllEmptyValues(): self
    {
        return $this->where(
            fn (self $query) => LocaleCollection::each(
                fn (Locale $locale) => $query->whereNull("value->{$locale->locale()}")
            )
        );
    }

    public function byFilledInValues(): self
    {
        return $this->where(
            fn (self $query) => LocaleCollection::each(
                fn (Locale $locale) => $query->whereNotNull("value->{$locale->locale()}")
            )
        );
    }
}
