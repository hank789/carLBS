@extends('backend.layouts.app')

@section('title', __('labels.backend.access.users.management') . ' | ' . __('labels.backend.access.users.view'))

@section('breadcrumb-links')
    @include('backend.auth.user.includes.breadcrumb-links')
@endsection

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
                                        <a href="#" class="btn btn-white btn-xs float-right">编辑行程</a>
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
                                                    <li><a class="nav-link" href="#tab-2" data-toggle="tab">卸货记录</a></li>
                                                    <li><a class="nav-link" href="#tab-1" data-toggle="tab">突发事件</a></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="panel-body">

                                            <div class="tab-content">
                                                <div class="tab-pane" id="tab-1">
                                                    <div class="feed-activity-list">
                                                        <div class="feed-element">
                                                            <a href="#" class="float-left">
                                                                <img alt="image" class="rounded-circle" src="img/a2.jpg">
                                                            </a>
                                                            <div class="media-body ">
                                                                <small class="float-right">2小时</small>
                                                                <strong>小明</strong> 发布消息 <strong>小红</strong> <br>
                                                                <small class="text-muted">今天下午2:10</small>
                                                                <div class="well">
                                                                    时间从何而来？为什么时间似乎是流动的？
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="feed-element">
                                                            <a href="#" class="float-left">
                                                                <img alt="image" class="rounded-circle" src="img/a3.jpg">
                                                            </a>
                                                            <div class="media-body ">
                                                                <small class="float-right">2小时</small>
                                                                <strong>小明</strong> 添加1张照片 <strong>我的相册</strong> <br>
                                                                <small class="text-muted">2天前在上午8:30</small>
                                                            </div>
                                                        </div>
                                                        <div class="feed-element">
                                                            <a href="#" class="float-left">
                                                                <img alt="image" class="rounded-circle" src="img/a4.jpg">
                                                            </a>
                                                            <div class="media-body ">
                                                                <small class="float-right text-navy">5小时</small>
                                                                <strong>小明</strong> 发布了文章 <strong>我的世界有多大</strong> <br>
                                                                <small class="text-muted">昨天1:21</small>
                                                                <div class="actions">
                                                                    <a class="btn btn-xs btn-white"><i class="fa fa-thumbs-up"></i> 赞 </a>
                                                                    <a class="btn btn-xs btn-white"><i class="fa fa-heart"></i> 喜欢</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="feed-element">
                                                            <a href="#" class="float-left">
                                                                <img alt="image" class="rounded-circle" src="img/a5.jpg">
                                                            </a>
                                                            <div class="media-body ">
                                                                <small class="float-right">2小时</small>
                                                                <strong>小明</strong> 发布消息 <strong>小红</strong> <br>
                                                                <small class="text-muted">昨天下午5:20</small>
                                                                <div class="well">
                                                                    时间从何而来？为什么时间似乎是流动的？
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="feed-element">
                                                            <a href="#" class="float-left">
                                                                <img alt="image" class="rounded-circle" src="img/profile.jpg">
                                                            </a>
                                                            <div class="media-body ">
                                                                <small class="float-right">23小时</small>
                                                                <strong>小明</strong> 发布了文章 <strong>我的世界有多大</strong> <br>
                                                                <small class="text-muted">2天前在7:58</small>
                                                            </div>
                                                        </div>
                                                        <div class="feed-element">
                                                            <a href="#" class="float-left">
                                                                <img alt="image" class="rounded-circle" src="img/a7.jpg">
                                                            </a>
                                                            <div class="media-body ">
                                                                <small class="float-right">46小时</small>
                                                                <strong>小明</strong> 添加1张照片 <strong>我的相册</strong> <br>
                                                                <small class="text-muted">3天前在7:58</small>
                                                            </div>
                                                        </div>
                                                    </div>

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
                    <strong>@lang('labels.backend.access.users.tabs.content.overview.created_at'):</strong> {{ timezone()->convertToLocal($main->created_at) }} ({{ $main->created_at->diffForHumans() }}),
                    <strong>@lang('labels.backend.access.users.tabs.content.overview.last_updated'):</strong> {{ timezone()->convertToLocal($main->updated_at) }} ({{ $main->updated_at->diffForHumans() }})
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
        });
    </script>
@endsection
