<?php

use Illuminate\Support\HtmlString;

if (! function_exists('__html')) {
    /**
     * Translate the given html message.
     */
    function __html(?string $key, array $replace = [], ?string $locale = null): HtmlString
    {
        return new HtmlString(__($key, $replace, $locale));
    }
}
