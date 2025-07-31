<?php

return [
    'trans_functions' => [
        '__',
        'trans',
        'trans_choice',
        'Lang::get',
        'Lang::choice',
        '@lang',
        '@choice',
    ],
    'html_trans_functions' => [
        '__html',
    ],
    'exclude_folders' => [
        'storage',
        'node_modules',
        'database',
        'lang',
        'vendor/symfony',
        'tests',
    ],

    'skip_export_to_lang' => (bool) env('SKIP_EXPORT_TO_LANG', false),
];
