<!DOCTYPE html>
<!-- saved from url=(0021)https://www.zsxq.com/ -->
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>车百讯</title>
    <meta name="description" content="车百讯">
    <meta name="keywords" content="车百讯">
    <link rel="shortcut icon" href="{{ asset('img/favicon_32.ico',config('app.use_ssl')) }}">
    <link rel="icon" href="{{ asset('img/favicon_32.ico',config('app.use_ssl')) }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css',config('app.use_ssl')) }}">

</head>

<body>
<div class="header">
    <div class="header-con">
        <h1 class="logo"><a href="#">车百讯</a></h1>
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
                <div class="footer-down">
                    <div class="footer-iphone">
                        <p class="down-txt">下载车百讯 App</p>
                        <a href="#"><span class="andriod">安卓版下载</span></a>
                        <a href="#"><span class="ios">iOS 版下载</span></a>
                    </div>
                    <div class="footer-qrcode">
                        <img src="{{ asset('img/qrcode@2x.png',config('app.use_ssl')) }}" width="100" height="100">
                    </div>
                </div>
                <div class="banner-tips">
                    <p>支持的系统版本：</p>
                    <p>- iOS 9.0 或更高版本</p>
                    <p>- Android 4.1 - 8.1</p>
                </div>
            </div>
            <div class="banner-right">
                <img src="{{ asset('img/iphone-x@2x.png',config('app.use_ssl')) }}" width="562" height="572">
            </div>
        </div>
    </div>
</div>
<div class="footer">
    <div class="footer-w">
        <div class="protocol-con">
            <ul class="protocol">

            </ul>
            <p class="copyright">版权所有©车百讯&nbsp;中讯智慧物联信息科技(苏州)有限公司&nbsp;&nbsp;<a href="http://www.miitbeian.gov.cn/" target="_blank">苏ICP备19008789号-1</a></p>
        </div>
    </div>
</div>

</body>
</html>
