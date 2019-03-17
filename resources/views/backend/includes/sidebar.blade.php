<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-title">
                @lang('menus.backend.sidebar.general')
            </li>
            <li class="nav-item">
                <a class="nav-link {{ active_class(Active::checkUriPattern('admin/dashboard')) }}" href="{{ route('admin.dashboard') }}">
                    <i class="nav-icon icon-speedometer"></i> @lang('menus.backend.sidebar.dashboard')
                </a>
            </li>

            <li class="nav-title">
                车队管理
            </li>
            @can('司机管理')
            <li class="nav-item">
                <a class="nav-link {{ active_class(Active::checkUriPattern('admin/transport/user*')) }}" href="{{ route('admin.transport.user.index') }}">
                    <i class="nav-icon icon-people"></i> 司机管理
                </a>
            </li>
            @endcan
            @can('行程管理')
            <li class="nav-item">
                <a class="nav-link {{ active_class(Active::checkUriPattern('admin/transport/main*') || Active::checkUriPattern('admin/transport/sub*')) }}" href="{{ route('admin.transport.main.index') }}">
                    <i class="nav-icon fa fa-car"></i> 行程管理
                </a>
            </li>
            @endcan
            @can('在线车辆')
            <li class="nav-item">
                <a class="nav-link" href="/manager.html" target="_blank">
                    <i class="nav-icon icon-location-pin"></i> 在线车辆
                </a>
            </li>
            @endcan
            @if ($logged_in_user->isAdmin())
            <li class="nav-title">
                权限管理
            </li>
            <li class="nav-item">
                <a class="nav-link {{ active_class(Active::checkUriPattern('admin/company/user*')) }}" href="{{ route('admin.company.user.index') }}">
                    <i class="nav-icon icon-user"></i> 账户管理
                </a>
            </li>
            @endif

            @if ($logged_in_user->isSuperAdmin())
                <li class="nav-title">
                    @lang('menus.backend.sidebar.system')
                </li>
                <li class="nav-item nav-dropdown {{ active_class(Active::checkUriPattern('admin/auth*'), 'open') }}">
                    <a class="nav-link nav-dropdown-toggle {{ active_class(Active::checkUriPattern('admin/auth*')) }}" href="#">
                        <i class="nav-icon icon-user"></i> @lang('menus.backend.access.title')

                        @if ($pending_approval > 0)
                            <span class="badge badge-danger">{{ $pending_approval }}</span>
                        @endif
                    </a>

                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/auth/user*')) }}" href="{{ route('admin.auth.user.index') }}">
                                <i class="nav-icon fa fa-circle-notch"></i> @lang('labels.backend.access.users.management')

                                @if ($pending_approval > 0)
                                    <span class="badge badge-danger">{{ $pending_approval }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/auth/role*')) }}" href="{{ route('admin.auth.role.index') }}">
                                <i class="nav-icon fa fa-circle-notch"></i> @lang('labels.backend.access.roles.management')
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active_class(Active::checkUriPattern('admin/version*')) }}" href="{{ route('admin.version.index') }}">
                        <i class="nav-icon fa fa-file"></i> App版本管理
                    </a>
                </li>

                <li class="divider"></li>

                <li class="nav-item nav-dropdown {{ active_class(Active::checkUriPattern('admin/log-viewer*'), 'open') }}">
                    <a class="nav-link nav-dropdown-toggle {{ active_class(Active::checkUriPattern('admin/log-viewer*')) }}" href="#">
                        <i class="nav-icon icon-list"></i> @lang('menus.backend.log-viewer.main')
                    </a>

                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/log-viewer')) }}" href="{{ route('log-viewer::dashboard') }}">
                                <i class="nav-icon fa fa-circle-notch"></i> @lang('menus.backend.log-viewer.dashboard')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/log-viewer/logs*')) }}" href="{{ route('log-viewer::logs.list') }}">
                                <i class="nav-icon fa fa-circle-notch"></i> @lang('menus.backend.log-viewer.logs')
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
    </nav>

    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div><!--sidebar-->
