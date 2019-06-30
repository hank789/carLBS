@extends('backend.layouts.app')

@section('title', app_display_name() . ' | ' . __('labels.backend.access.users.management'))

@section('breadcrumb-links')
    @include('backend.company.user.includes.breadcrumb-links')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-5">
                <h4 class="card-title mb-0">
                    {{ __('labels.backend.access.users.management') }} <small class="text-muted">{{ __('labels.backend.access.users.active') }}</small>
                </h4>
            </div><!--col-->

            <div class="col-sm-7">
                <form name="searchForm" class="form-horizontal" action="{{ route('admin.company.user.index') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="filter" value="{{ $filter['filter']??'' }}">
                    <div class="form-group row">
                        <div class="col-4">
                            <input type="text" class="form-control" name="name" placeholder="姓名" value="{{ $filter['name']??'' }}"/>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" name="mobile" placeholder="手机号" value="{{ $filter['mobile']??'' }}"/>
                        </div>

                        <div class="col-4">
                            <div class="btn-toolbar float-right" role="toolbar">
                                <button type="submit" class="btn btn-primary">搜索</button>
                                <a href="{{ route('admin.company.user.create') }}" class="btn btn-success ml-1" data-toggle="tooltip" title="@lang('labels.general.create_new')"><i class="fas fa-plus-circle"></i></a>
                            </div>
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
                            <th>@lang('labels.backend.access.users.table.full_name')</th>
                            <th>手机号</th>
                            <th>公司</th>
                            <th>状态</th>
                            <th>@lang('labels.backend.access.users.table.roles')</th>
                            <th>@lang('labels.backend.access.users.table.other_permissions')</th>
                            <th>@lang('labels.backend.access.users.table.last_updated')</th>
                            <th>@lang('labels.general.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->last_name.$user->first_name }}</td>
                                <td>{{ $user->mobile }}</td>
                                <td>{{ $user->company?$user->company->company_name:'' }}</td>
                                <td>{!! $user->status_label !!}</td>
                                <td>{!! $user->roles_label !!}</td>
                                <td>{!! $user->permissions_label !!}</td>
                                <td>{{ $user->updated_at->diffForHumans() }}</td>
                                <td>{!! $user->company_action_buttons !!}</td>
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
                    {!! $users->total() !!} {{ trans_choice('labels.backend.access.users.table.total', $users->total()) }}
                </div>
            </div><!--col-->

            <div class="col-8">
                <div class="float-right">
                    {!! str_replace('/?', '?', $users->appends($filter??[])->render()) !!}
                </div>
            </div><!--col-->
        </div><!--row-->
    </div><!--card-body-->
</div><!--card-->
@endsection
