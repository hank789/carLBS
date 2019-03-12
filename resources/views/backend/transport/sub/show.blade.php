@extends('backend.layouts.app')

@section('title', '行程管理 | 查看司机行程')

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

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="m-b-md">
                                        <h4>司机行程信息</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right"><dt>状态:</dt> </div>
                                        <div class="col-sm-8 text-sm-left"><dd class="mb-1">{!! $sub->status_label !!}</dd></div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right"><dt>司机姓名:</dt> </div>
                                        <div class="col-sm-8 text-sm-left"><dd class="mb-1">{{ $sub->apiUser->name }}</dd> </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right"><dt>司机手机:</dt> </div>
                                        <div class="col-sm-8 text-sm-left"> <dd class="mb-1">{{ $sub->apiUser->mobile }}</dd></div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right"><dt>车牌号:</dt> </div>
                                        <div class="col-sm-8 text-sm-left"> <dd class="mb-1">{{ $sub->transportEntity->car_number }}</dd></div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right"> <dt>出发时间:</dt></div>
                                        <div class="col-sm-8 text-sm-left"> <dd class="mb-1">{{ $sub->transport_start_time }}</dd></div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>出发地:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $sub->transport_start_place }}</dd>
                                        </div>
                                    </dl>
                                </div>
                                <div class="col-lg-6" id="cluster_info">
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>目的地:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $sub->transport_end_place }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>突发事件:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $sub->getTransportEventCount() }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>卸货次数:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $sub->getTransportXiehuoCount() }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>最后定位时间:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $sub->last_loc_time }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>最后定位地址:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $sub->transport_goods['lastPosition']['formatted_address']??'' }}</dd>
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
                                                    <li><a class="nav-link active" href="#tab-1" data-toggle="tab">行程记录</a></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="panel-body">

                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab-1">

                                                    <div>

                                                        <div id="vertical-timeline" class="vertical-container dark-timeline center-orientation">

                                                            @foreach($timeline as $item)
                                                                <div class="vertical-timeline-block">
                                                                    <div class="vertical-timeline-icon {{$item['bg_color']}}">
                                                                        <i class="fa {{$item['icon']}}"></i>
                                                                    </div>

                                                                    <div class="vertical-timeline-content">
                                                                        <h2>{{ $item['title'] }}</h2>
                                                                        <p>{{ $item['desc'] }}</p>
                                                                        <span class="vertical-date">
                                                                            {{ $item['created_at'] }} <br/>
                                                                            <small>{{ $item['place'] }}</small>
                                                                            <p>
                                                                            @foreach($item['images'] as $image)
                                                                                <a target="_blank" href="{{ $image }}">
                                                                                    <img src="{{ $image }}" style="width: 100px;height: 100px;margin: 5px;">
                                                                                </a>
                                                                            @endforeach
                                                                            </p>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
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
            </div>

        </div><!--row-->
    </div><!--card-body-->

    <div class="card-footer">
        <div class="row">
            <div class="col">
                <small class="float-right text-muted">
                    <strong>@lang('labels.backend.access.users.tabs.content.overview.created_at'):</strong> {{ $sub->created_at }} ({{ $sub->created_at->diffForHumans() }}),
                    <strong>@lang('labels.backend.access.users.tabs.content.overview.last_updated'):</strong> {{ $sub->updated_at }} ({{ $sub->updated_at->diffForHumans() }})
                </small>
            </div><!--col-->
        </div><!--row-->
    </div><!--card-footer-->
</div><!--card-->
@endsection
