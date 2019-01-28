@extends('backend.layouts.app')

@section('title', app_name() . ' | 行程管理')

@section('breadcrumb-links')
    @include('backend.transport.main.includes.breadcrumb-links')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-4">
                <h4 class="card-title mb-0">
                    行程管理 <small class="text-muted">车队出发前，必须新建行程，并将行程ID告诉司机</small>
                </h4>
            </div><!--col-->

            <div class="col-sm-8">
                <form name="searchForm" class="form-horizontal" action="{{ route('admin.transport.main.index') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="filter" value="{{ $filter['filter']??'' }}">
                    <div class="form-group row">
                        <div class="col-2">
                            <input type="text" class="form-control" name="transport_number" placeholder="行程ID" value="{{ $filter['transport_number']??'' }}"/>
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" name="transport_end_place" placeholder="目的地" value="{{ $filter['transport_end_place']??'' }}"/>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" id="date_range" name="transport_start_time" placeholder="出发时间" value="{{ $filter['transport_start_time']??'' }}"/>
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary">搜索</button>
                            <a href="{{ route('admin.auth.user.create') }}" class="btn btn-success ml-1" data-toggle="tooltip" title="@lang('labels.general.create_new')"><i class="fas fa-plus-circle"></i></a>
                        </div>
                    </div>
                </form>
            </div><!--col-->
        </div><!--row-->

        <div class="row mt-4">
            <div class="col">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>行程ID</th>
                            <th>出发地</th>
                            <th>目的地</th>
                            <th>联系人</th>
                            <th>联系电话</th>
                            <th>开始时间</th>
                            <th>货物</th>
                            <th>创建者</th>
                            <th>创建时间</th>
                            <th>状态</th>
                            <th>@lang('labels.general.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $item)
                            <tr>
                                <td>{{ $item->transport_number }}</td>
                                <td>{{ $item->transport_start_place }}</td>
                                <td>{{ $item->transport_end_place }}</td>
                                <td>{{ $item->transport_contact_people }}</td>
                                <td>{{ $item->transport_contact_phone }}</td>
                                <td>{{ $item->transport_start_time }}</td>
                                <td>{{ str_limit($item->transport_goods,50) }}</td>
                                <td>{{ $item->systemUser->fullname }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td>{!! $item->status_label !!}</td>
                                <td>{!! $item->action_buttons !!}</td>
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
                    <span class="total-num">共 {{ $list->total() }} 条数据</span>
                    {!! str_replace('/?', '?', $list->appends($filter)->render()) !!}
                </div>
            </div><!--col-->
        </div><!--row-->
    </div><!--card-body-->
</div><!--card-->
@endsection

@section('script')
    <script type="text/javascript">
        /*daterange控件*/
        $('#date_range').daterangepicker({
            format: 'YYYY-MM-DD',
            locale: {
                applyLabel: '确认',
                cancelLabel: '取消',
                fromLabel: '从',
                toLabel: '到',
                weekLabel: '星期',
                customRangeLabel: '自定义范围',
                daysOfWeek: '日_一_二_三_四_五_六'.split('_'),
                monthNames: '1月_2月_3月_4月_5月_6月_7月_8月_9月_10月_11月_12月'.split('_'),
                firstDay: 1
            }
        });
    </script>
@endsection
