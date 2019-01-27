@extends('backend.layouts.app')

@section('title', app_name() . ' | 司机管理')

@section('breadcrumb-links')
    @include('backend.transport.user.includes.breadcrumb-links')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-5">
                <h4 class="card-title mb-0">
                    司机管理 <small class="text-muted">管理司机信息</small>
                </h4>
            </div><!--col-->

            <div class="col-sm-7">
                <form name="searchForm" class="form-horizontal" action="{{ route('admin.transport.user.index') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="filter" value="{{ $filter['filter']??'' }}">
                    <div class="form-group row">
                        <div class="col-6">
                            <input type="text" class="form-control" name="nameOrMobile" placeholder="姓名或手机号" value="{{ $filter['nameOrMobile']??'' }}"/>
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
                            <th>@lang('labels.backend.access.users.table.full_name')</th>
                            <th>手机号</th>
                            <th>状态</th>
                            <th>行程数</th>
                            <th>注册时间</th>
                            <th>@lang('labels.backend.access.users.table.last_updated')</th>
                            <th>@lang('labels.general.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->mobile }}</td>
                                <td>{!! $user->status_label !!}</td>
                                <td>{{ $user->trip_number }}</td>
                                <td>{{ $user->created_at }}</td>
                                <td>{{ $user->updated_at->diffForHumans() }}</td>
                                <td>{!! $user->action_buttons !!}</td>
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
                    <span class="total-num">共 {{ $users->total() }} 条数据</span>
                    {!! str_replace('/?', '?', $users->appends($filter)->render()) !!}
                </div>
            </div><!--col-->
        </div><!--row-->
    </div><!--card-body-->
</div><!--card-->
@endsection
