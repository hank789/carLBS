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
        <title>@yield('title', app_name())</title>
        <meta name="description" content="@yield('meta_description', 'Laravel 5 Boilerplate')">
        <meta name="author" content="@yield('meta_author', 'Anthony Rappa')">
    @yield('meta')

    {{-- See https://laravel.com/docs/5.5/blade#stacks for usage --}}
    @stack('before-styles')

    <!-- Check if the language is set to RTL, so apply the RTL layouts -->
        <!-- Otherwise apply the normal LTR layouts -->
        {{ style(mix('css/backend.css')) }}

        @stack('after-styles')
        @yield('head-script')
    </head>
    <body class="app flex-row align-items-center  pace-done">


    <div class="container">
        @include('includes.partials.messages')
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card-group">
                    <div class="card p-4">
                        <div class="card-body">

                            {{ html()->form('POST', route('frontend.auth.login.post'))->open() }}
                            <h1>登陆</h1>
                            <p class="text-muted">{{app_display_name()}}管理后台</p>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
<span class="input-group-text">
<i class="icon-user"></i>
</span>
                                </div>
                                <input class="form-control" id="email" name="email" type="email" placeholder="输入您的电子邮件">
                            </div>
                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
<span class="input-group-text">
<i class="icon-lock"></i>
</span>
                                </div>
                                <input class="form-control" name="password" type="password" placeholder="输入您的密码">
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <button class="btn btn-primary px-4" type="submit">登陆</button>
                                </div>
                                <div class="col-6 text-right">
                                    <a href="{{ route('frontend.auth.password.reset') }}" target="_blank" class="btn btn-link px-0">忘记密码?</a>
                                </div>
                            </div>
                            {{ html()->form()->close() }}
                        </div>
                    </div>
                    <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
                        <div class="card-body text-center">
                            <div>
                                <p><img class="navbar-brand-full" src="{{ asset('img/backend/brand/logo.svg') }}" width="150" height="150" alt="CoreUI Logo"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>