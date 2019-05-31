@extends('backend.layouts.app')

@section('title', app_display_name() . ' | 车辆管理')


@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-5">
                <h4 class="card-title mb-0">
                    车辆管理 <small class="text-muted">查看车辆信息</small>
                </h4>
            </div><!--col-->

            <div class="col-sm-7">
                <form name="searchForm" class="form-horizontal" action="{{ route('admin.transport.car.index') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="filter" value="{{ $filter['filter']??'' }}">
                    <div class="form-group row float-right">
                        <div class="col-8">
                            <input type="text" class="form-control" name="car_number" placeholder="车牌号" value="{{ $filter['car_number']??'' }}"/>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary">搜索</button>
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
                            <th>ID</th>
                            <th>车牌号</th>
                            <th>司机姓名</th>
                            <th>司机手机</th>
                            <th>最后定位地址</th>
                            <th>最后定位时间</th>
                            <th>@lang('labels.general.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cars as $car)
                            <tr>
                                <td>{{ $car->id }}</td>
                                <td>{{ $car->car_number }}</td>
                                <td>{{ $car->entity_info['lastSub']['username']??'' }}</td>
                                <td>{{ $car->entity_info['lastSub']['phone']??'' }}</td>
                                <td>{{ $car->entity_info['lastPosition']['formatted_address']??'' }}</td>
                                <td>{{ $car->last_loc_time }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="查看">
                                        @if (isset($car->entity_info['lastSub']['sub_id']))
                                            <a href="{{ route('admin.transport.sub.show', $car->entity_info['lastSub']['sub_id']) }}" data-toggle="tooltip" data-placement="top" title="查看" class="btn btn-info"><i class="fas fa-eye"></i></a>
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
                    <span class="total-num">共 {{ $cars->total() }} 条数据</span>
                    {!! str_replace('/?', '?', $cars->appends($filter)->render()) !!}
                </div>
            </div><!--col-->
        </div><!--row-->
    </div><!--card-body-->
</div><!--card-->
@endsection
