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
    <title>@yield('title', '运输管理系统')</title>
    <meta name="description" content="@yield('meta_description', app_display_name())">
    <meta name="author" content="@yield('meta_author', app_display_name())">
    <link rel="shortcut icon" href="{{ asset('img/favicon_32.ico',config('app.use_ssl')) }}">
    <link rel="icon" href="{{ asset('img/favicon_32.ico',config('app.use_ssl')) }}">
    @yield('meta')

    {{-- See https://laravel.com/docs/5.5/blade#stacks for usage --}}
    @stack('before-styles')

    <!-- Check if the language is set to RTL, so apply the RTL layouts -->
    <!-- Otherwise apply the normal LTR layouts -->
    {{ style(mix('css/backend.css'),[],config('app.use_ssl')) }}

    @stack('after-styles')
    @yield('head-script')
</head>

<body class="{{ config('backend.body_classes') }}">
    @include('backend.includes.header')

    <div class="app-body">
        @include('backend.includes.sidebar')

        <main class="main">
            @include('includes.partials.logged-in-as')
            {!! Breadcrumbs::render() !!}

            <div class="container-fluid">
                <div class="animated fadeIn">
                    <div class="content-header">
                        @yield('page-header')
                    </div><!--content-header-->

                    @include('includes.partials.messages')
                    @yield('content')
                </div><!--animated-->
            </div><!--container-fluid-->
        </main><!--main-->
        <!-- backend.includes.aside -->

    </div><!--app-body-->

    <!-- Scripts -->
    @stack('before-scripts')
    {!! script(mix('js/manifest.js'),[],config('app.use_ssl')) !!}
    {!! script(mix('js/vendor.js'),[],config('app.use_ssl')) !!}
    {!! script(mix('js/backend.js'),[],config('app.use_ssl')) !!}
    @stack('after-scripts')
    @yield('script')
</body>
</html>
