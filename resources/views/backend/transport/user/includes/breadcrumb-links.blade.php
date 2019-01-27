<li class="breadcrumb-menu">
    <div class="btn-group" role="group" aria-label="Button group">
        <div class="dropdown">
            <a class="btn dropdown-toggle" href="#" role="button" id="breadcrumb-dropdown-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">刷选</a>

            <div class="dropdown-menu" aria-labelledby="breadcrumb-dropdown-1">
                <a class="dropdown-item" href="{{ route('admin.transport.user.index',['filter'=>'all']) }}">所有司机</a>
                <a class="dropdown-item" href="{{ route('admin.transport.user.index',['filter'=>'active']) }}">已激活司机</a>
                <a class="dropdown-item" href="{{ route('admin.transport.user.index',['filter'=>'deactivated']) }}">已禁止的司机</a>
            </div>
        </div><!--dropdown-->

        <!--<a class="btn" href="#">Static Link</a>-->
    </div><!--btn-group-->
</li>
