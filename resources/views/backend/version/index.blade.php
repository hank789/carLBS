@extends('backend.layouts.app')

@section('title', app_display_name() . ' | App版本管理')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-5">
                <h4 class="card-title mb-0">
                    版本管理 <small class="text-muted">app升级用</small>
                </h4>
            </div><!--col-->
            <div class="col-sm-7">
                <div class="btn-toolbar float-right" role="toolbar" aria-label="@lang('labels.general.toolbar_btn_groups')">
                    <a href="{{ route('admin.version.create') }}" class="btn btn-success ml-1" data-toggle="tooltip" title="@lang('labels.general.create_new')"><i class="fas fa-plus-circle"></i></a>
                </div><!--btn-toolbar-->
            </div><!--col-->
        </div><!--row-->

        <div class="row mt-4">
            <div class="col">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>版本号</th>
                            <th>是否ios强更</th>
                            <th>是否android强更</th>
                            <th>时间</th>
                            <th>包地址</th>
                            <th>更新内容</th>
                            <th>状态</th>
                            <th>@lang('labels.general.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($versions as $version)
                            <tr>
                                <td>{{ $version->app_version }}</td>
                                <td>{{ $version->is_ios_force }}</td>
                                <td>{{ $version->is_android_force }}</td>
                                <td>{{ ($version->created_at) }}</td>
                                <td>{{ $version->package_url }}</td>
                                <td>{{ $version->update_msg }}</td>
                                <td>{!! $version->status_label !!}</td>
                                <td>
                                    <div class="btn-group-xs" >
                                        <a class="btn btn-default" href="{{ route('admin.version.edit',['id'=>$version->id]) }}" data-toggle="tooltip" title="编辑"><i class="fa fa-edit"></i></a>
                                        @if ($version->status != 1)
                                            <button class="btn btn-default btn-confirm" data-source_id="{{ $version->id }}" href="javascript:void(0)" data-toggle="tooltip" title="通过审核"><i class="fa fa-check"></i></button>
                                        @endif
                                        @if ($version->status == 1)
                                            <button class="btn btn-default btn-suspend" data-source_id="{{ $version->id }}" href="javascript:void(0)" data-toggle="tooltip" title="审核不通过"><i class="fa fa-trash"></i></button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div><!--col-->
        </div><!--row-->
        <div class="row">
            <div class="col-4">
                <div class="float-left">

                </div>
            </div><!--col-->

            <div class="col-8">
                <div class="float-right">
                    <span class="total-num">共 {{ $versions->total() }} 条数据</span>
                    {!! str_replace('/?', '?', $versions->render()) !!}
                </div>
            </div><!--col-->
        </div><!--row-->
    </div><!--card-body-->
</div><!--card-->
@endsection

@section('script')
    <script type="text/javascript">
        $(".btn-confirm").click(function(){
            if(!confirm('确认审核通过选中项')){
                return false;
            }
            $(this).button('loading');
            var source_id = $(this).data('source_id');
            $.get('/admin/version/verify/' + source_id,{},function(msg){
                console.log(msg);
                window.location.reload();
            });
        });
        $(".btn-suspend").click(function(){
            if(!confirm('确认禁止选中项')){
                return false;
            }
            $(this).button('loading');
            var source_id = $(this).data('source_id');
            $.get('/admin/version/destroy/' + source_id,{},function(msg){
                window.location.reload();
            });
        });
    </script>
@endsection
