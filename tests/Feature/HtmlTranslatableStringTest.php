<?php

use Illuminate\Support\HtmlString;

it('has a __html helper', function () {
    expect(function_exists('__html'))->toBeTrue();
});

it('returns a html string', function () {
    expect(__html('test'))->toBeInstanceOf(HtmlString::class);
});
