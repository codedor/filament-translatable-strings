<?php

namespace Wotz\Translations\Tests\Http\Controllers;

use Illuminate\Support\Facades\Lang;

class TestController
{
    public function index()
    {
        trans('test.trans');
        trans_choice('test.trans choice', 2);
        Lang::get('test.lang get');
        Lang::choice('test.lang choice');

        trans('trans');
        trans_choice('trans choice', 2);
        Lang::get('lang get');
        Lang::choice('lang choice');

        trans('package::test.trans');
        trans_choice('package::test.trans choice', 2);
        Lang::get('package::test.lang get');
        Lang::choice('package::test.lang choice');
    }
}
