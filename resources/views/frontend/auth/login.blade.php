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
        <link rel="shortcut icon" href="{{ $favicon }}">
        <link rel="icon" href="{{ $favicon }}">
        <meta name="description" content="@yield('meta_description', app_display_name())">
        <meta name="author" content="@yield('meta_author', app_display_name())">
    @yield('meta')

    {{-- See https://laravel.com/docs/5.5/blade#stacks for usage --}}
    @stack('before-styles')

    <!-- Check if the language is set to RTL, so apply the RTL layouts -->
        <!-- Otherwise apply the normal LTR layouts -->
        {{ style(mix('css/backend.css'),[],config('app.use_ssl')) }}
        {{ style(('css/plugins/toastr/toastr.min.css'),[],config('app.use_ssl')) }}

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

                            {{ html()->form('POST', route('frontend.auth.login.codeLogin'))->open() }}
                            <h1>登陆</h1>
                            <p class="text-muted">{{app_display_name()}}管理后台</p>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                    <i class="icon-phone"></i>
                                    </span>
                                </div>
                                <input class="form-control" id="mobile" name="mobile" type="number" required placeholder="输入您的手机号码">
                                <div id="mobile_invalid" class="invalid-feedback"></div>
                            </div>
                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                    <i class="icon-lock"></i>
                                    </span>
                                </div>
                                <input class="form-control" id="phoneCode" name="phoneCode" type="number" required placeholder="验证码">
                                <span class="input-group-append">
                                    <button type="button" class="btn btn-info" onclick="sendYzm()" id="btn-yzm">获取验证码</button>
                                </span>
                                <div id="phoneCode_invalid" class="invalid-feedback"></div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <button class="btn btn-primary px-4" type="submit">登陆</button>
                                </div>
                                <div class="col-6 text-right">

                                </div>
                            </div>
                            {{ html()->form()->close() }}
                        </div>
                    </div>
                    <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
                        <div class="card-body text-center">
                            <div>
                                <p><img class="navbar-brand-full" src="{{ $logo }}" width="150" height="150" alt="Logo"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! script(('js/plugins/jquery-3.1.1.min.js'),[],config('app.use_ssl')) !!}
    {!! script(('js/plugins/toastr/toastr.min.js'),[],config('app.use_ssl')) !!}

    <script type="text/javascript">
        function sendYzm() {
            $('#btn-yzm').attr('disabled','disabled');
            var phone = $('#mobile').val();
            console.log(phone);
            if (!phone) {
                $('#mobile').addClass('is-invalid');
                $('#mobile_invalid').html('手机号有误');
                $('#btn-yzm').removeAttr('disabled');
                return;
            } else {
                $('#mobile').removeClass('is-invalid');
                $('#mobile_invalid').html('');
            }
            $.post('/api/auth/sendPhoneCode/',{mobile: phone,type: 'backend_login'},function(msg){
                console.log(msg);
                if (msg.code !== 1000) {
                    $('#mobile').addClass('is-invalid');
                    $('#mobile_invalid').html(msg.message);
                    $('#btn-yzm').removeAttr('disabled');
                } else {
                    toastr.success('验证码发送成功！');
                    var counter = 59;
                    $('#btn-yzm').html(counter + '秒');
                    var timer = setInterval(() => {
                        counter--;
                        $('#btn-yzm').html(counter + '秒');
                        if (counter == 0) {
                            $('#btn-yzm').html('获取验证码');
                            $('#btn-yzm').removeAttr('disabled');
                            clearInterval(timer)
                        }
                    }, 1000)
                }
            });
        }
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "progressBar": true,
            "preventDuplicates": false,
            "positionClass": "toast-top-center",
            "onclick": null,
            "showDuration": "400",
            "hideDuration": "1000",
            "timeOut": "7000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>
    </body>
    </html>