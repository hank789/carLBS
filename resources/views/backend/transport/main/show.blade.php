@extends('backend.layouts.app')

@section('title', '行程管理 | 查看行程')

@section('head-script')
    {{ style(('css/plugins/dataTables/datatables.min.css')) }}
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">

            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="m-b-md">
                                        @if ($main->transport_status <= 0)
                                            <a href="{{ route('admin.transport.main.edit',$main->id) }}" class="btn btn-white btn-xs float-right">编辑行程</a>
                                        @endif
                                        <h2>行程ID：{{$main->transport_number}}</h2>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right"><dt>状态:</dt> </div>
                                        <div class="col-sm-8 text-sm-left"><dd class="mb-1">{!! $main->status_label !!}</dd></div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right"><dt>创建者:</dt> </div>
                                        <div class="col-sm-8 text-sm-left"><dd class="mb-1">{{ $main->systemUser->fullname }}</dd> </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right"><dt>出发地:</dt> </div>
                                        <div class="col-sm-8 text-sm-left"> <dd class="mb-1">{{ $main->transport_start_place }}</dd></div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right"><dt>目的地:</dt> </div>
                                        <div class="col-sm-8 text-sm-left"> <dd class="mb-1">{{ $main->transport_end_place }}</dd></div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right"> <dt>行程出发时间:</dt></div>
                                        <div class="col-sm-8 text-sm-left"> <dd class="mb-1">{{ $main->transport_start_time }}</dd></div>
                                    </dl>

                                </div>
                                <div class="col-lg-6" id="cluster_info">

                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>行程联系人:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $main->transport_contact_people }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>行程联系人电话:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $main->transport_contact_phone }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>突发事件:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $main->getTransportEventCount() }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>卸货次数:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $main->getTransportXiehuoCount() }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>创建时间:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $main->created_at }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <dl class="row mb-0">
                                        <div class="col-sm-2 text-sm-right">
                                            <dt>货物信息:</dt>
                                        </div>
                                        <div class="col-sm-10 text-sm-left">
                                            <dd>
                                                {{ $main->transport_goods['transport_goods'] }}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                            <div class="row m-t-sm">
                                <div class="col-lg-12">
                                    <div class="panel blank-panel">
                                        <div class="panel-heading">
                                            <div class="panel-options">
                                                <ul class="nav nav-tabs">
                                                    <li><a class="nav-link active" href="#tab-2" data-toggle="tab">运输车辆</a></li>
                                                    <li><a class="nav-link" href="#tab-1" data-toggle="tab">卸货记录</a></li>
                                                    <li><a class="nav-link" href="#tab-3" data-toggle="tab">突发事件</a></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="panel-body">

                                            <div class="tab-content">
                                                <div class="tab-pane" id="tab-1">
                                                    <table class="table table-striped" id="table_transport_xiehuo">
                                                        <thead>
                                                        <tr>
                                                            <th>司机</th>
                                                            <th>电话</th>
                                                            <th>车牌号</th>
                                                            <th>类型</th>
                                                            <th>卸货地点</th>
                                                            <th>图片</th>
                                                            <th>货物信息</th>
                                                            <th>发生时间</th>
                                                        </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                                <div class="tab-pane active" id="tab-2">
                                                    <table class="table table-striped" id="table_transport_subs">
                                                        <thead>
                                                        <tr>
                                                            <th>司机</th>
                                                            <th>电话</th>
                                                            <th>车牌号</th>
                                                            <th>开始时间</th>
                                                            <th>紧急事件数</th>
                                                            <th>卸货次数</th>
                                                            <th>最后定位地址</th>
                                                            <th>状态</th>
                                                            <th>操作</th>
                                                        </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                                <div class="tab-pane" id="tab-3">
                                                    <table class="table table-striped" id="table_transport_events">
                                                        <thead>
                                                        <tr>
                                                            <th>司机</th>
                                                            <th>电话</th>
                                                            <th>车牌号</th>
                                                            <th>事件类型</th>
                                                            <th>事件地址</th>
                                                            <th>图片</th>
                                                            <th>事件描述</th>
                                                            <th>发生时间</th>
                                                        </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!--row-->
    </div><!--card-body-->

    <div class="card-footer">
        <div class="row">
            <div class="col">
                <small class="float-right text-muted">
                    <strong>@lang('labels.backend.access.users.tabs.content.overview.created_at'):</strong> {{ $main->created_at }} ({{ $main->created_at->diffForHumans() }}),
                    <strong>@lang('labels.backend.access.users.tabs.content.overview.last_updated'):</strong> {{ $main->updated_at }} ({{ $main->updated_at->diffForHumans() }})
                </small>
            </div><!--col-->
        </div><!--row-->
    </div><!--card-footer-->
</div><!--card-->
@endsection

@section('script')
    {!! script(('js/plugins/dataTables/datatables.min.js')) !!}
    {!! script(('js/plugins/dataTables/dataTables.bootstrap4.min.js')) !!}
    <script type="text/javascript">
        $(document).ready(function() {
            $('#table_transport_subs').DataTable({
                "ordering": false,
                "searching": false,
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('admin.transport.main.sublist',['id'=>$main->id]) }}"
            });
            $('#table_transport_events').DataTable({
                "ordering": false,
                "searching": false,
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('admin.transport.main.eventlist',['id'=>$main->id]) }}"
            });
            $('#table_transport_xiehuo').DataTable({
                "ordering": false,
                "searching": false,
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('admin.transport.main.xiehuolist',['id'=>$main->id]) }}"
            });
        });
    </script>
@endsection
