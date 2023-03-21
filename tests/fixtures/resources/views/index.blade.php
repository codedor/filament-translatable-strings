<html>
    <head>
        <title>App Name - @yield('title')</title>
    </head>
    <body>
        {{ __('test.underscore') }}
        {{ __html('test.underscore html') }}
        @lang('test.lang directive')
        @choice('test.choice directive')

        {{ __('underscore') }}
        {{ __html('underscore html') }}
        @lang('lang directive')
        @choice('choice directive')

        {{ __('package::test.underscore') }}
        {{ __html('package::test.underscore html') }}
        @lang('package::test.lang directive')
        @choice('package::test.choice directive')
    </body>
</html>
