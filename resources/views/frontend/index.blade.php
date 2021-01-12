<!DOCTYPE html>
<!-- saved from url=(0021)https://www.zsxq.com/ -->
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>中讯智慧物联信息科技</title>
    <meta name="description" content="中讯智慧物联信息科技">
    <meta name="keywords" content="中讯智慧物联信息科技">
    <link rel="shortcut icon" href="{{ asset('img/favicon_32_chebaixun.ico',config('app.use_ssl')) }}">
    <link rel="icon" href="{{ asset('img/favicon_32_chebaixun.ico',config('app.use_ssl')) }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css?v=1',config('app.use_ssl')) }}">

</head>

<body>
<div class="header">
    <div class="header-con">
        <h1 class="logoHome"><a href="#">中讯智慧物联信息科技</a></h1>
        <div class="menu">
            @guest
            <a href="{{route('frontend.auth.login')}}" class="href-dweb">@lang('navs.frontend.login')</a>
            @else
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="navbarDropdownMenuUser" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">{{ $logged_in_user->name }}</a>

                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuUser">
                        @can('后台登陆')
                            <a href="{{ route('admin.dashboard') }}" class="dropdown-item">@lang('navs.frontend.user.administration')</a>
                        @endcan
                        <a href="{{ route('frontend.auth.logout') }}" class="dropdown-item">@lang('navs.general.logout')</a>
                    </div>
                </li>
                @endguest
        </div>
    </div>
</div>
<div class="main">
    <div class="section1">
        <div class="w flex">
            <div class="banner-left">

            </div>

        </div>
    </div>
</div>
<div class="footer">
    <div class="footer-w">
        <div class="protocol-con">
            <ul class="protocol">

            </ul>
            <p class="copyright">版权所有©车百讯 &nbsp;中讯智慧物联信息科技(苏州)有限公司&nbsp;&nbsp;<a href="https://www.miit.gov.cn/" target="_blank">苏ICP备19008789号-1</a></p>
        </div>
    </div>
</div>

</body>
</html>
