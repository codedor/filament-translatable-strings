<?php

use Illuminate\Support\HtmlString;

if (! function_exists('__html')) {
    /**
     * Translate the given html message.
     */
    function __html(string|null $key, array|null $replace, string|null  $locale): string|array|null
    {
        return new HtmlString(__($key, $replace, $locale));
    }
}
