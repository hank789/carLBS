<!DOCTYPE html>
@langrtl
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
@else
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endlangrtl
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', app_display_name())</title>
        <meta name="description" content="@yield('meta_description', app_display_name())">
        <meta name="author" content="@yield('meta_author', app_display_name())">
        @yield('meta')

        {{-- See https://laravel.com/docs/5.5/blade#stacks for usage --}}
        @stack('before-styles')

        <!-- Check if the language is set to RTL, so apply the RTL layouts -->
        <!-- Otherwise apply the normal LTR layouts -->
        {{ style(mix('css/frontend.css'),[],config('app.use_ssl')) }}

        @stack('after-styles')
    </head>
    <body>
        <div id="app">
            @include('includes.partials.logged-in-as')
            @include('frontend.includes.nav')

            <div class="container">
                @include('includes.partials.messages')
                @yield('content')
            </div><!-- container -->
        </div><!-- #app -->

        <!-- Scripts -->
        @stack('before-scripts')
        {!! script(mix('js/manifest.js'),[],config('app.use_ssl')) !!}
        {!! script(mix('js/vendor.js'),[],config('app.use_ssl')) !!}
        {!! script(mix('js/frontend.js'),[],config('app.use_ssl')) !!}
        @stack('after-scripts')
    </body>
</html>
