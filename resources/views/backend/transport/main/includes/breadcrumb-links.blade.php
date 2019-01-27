<li class="breadcrumb-menu">
    <div class="btn-group" role="group" aria-label="Button group">
        <div class="dropdown">
            <a class="btn dropdown-toggle" href="#" role="button" id="breadcrumb-dropdown-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">刷选</a>

            <div class="dropdown-menu" aria-labelledby="breadcrumb-dropdown-1">
                <a class="dropdown-item" href="{{ route('admin.transport.main.index',['filter'=>'all']) }}">所有行程</a>
                <a class="dropdown-item" href="{{ route('admin.transport.main.index',['filter'=>'pending']) }}">未开始行程</a>
                <a class="dropdown-item" href="{{ route('admin.transport.main.index',['filter'=>'processing']) }}">进行中行程</a>
                <a class="dropdown-item" href="{{ route('admin.transport.main.index',['filter'=>'finished']) }}">已结束行程</a>
                <a class="dropdown-item" href="{{ route('admin.transport.main.index',['filter'=>'canceled']) }}">已取消行程</a>
            </div>
        </div><!--dropdown-->

        <!--<a class="btn" href="#">Static Link</a>-->
    </div><!--btn-group-->
</li>
