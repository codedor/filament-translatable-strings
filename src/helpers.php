<?php

use Illuminate\Support\HtmlString;

if (! function_exists('__html')) {
    /**
     * Translate the given html message.
     */
    function __html(?string $key, array $replace = [], string $locale = null): HtmlString
    {
        $string = __($key, $replace, $locale);

        // When using the codedor/filament-link-picker package, we need to parse the link picker
        if (function_exists('parse_link_picker_json')) {
            $string = parse_link_picker_json($string);
        }

        return new HtmlString($string);
    }
}
