@extends('backend.layouts.app')

@section('title', __('labels.backend.access.users.management') . ' | ' . __('labels.backend.access.users.view'))

@section('breadcrumb-links')
    @include('backend.auth.user.includes.breadcrumb-links')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-5">
                <h4 class="card-title mb-0">
                    @lang('labels.backend.access.users.management')
                    <small class="text-muted">@lang('labels.backend.access.users.view')</small>
                </h4>
            </div><!--col-->
        </div><!--row-->

        <div class="row mt-4 mb-4">
            <div class="col">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-expanded="true"><i class="fas fa-user"></i> @lang('labels.backend.access.users.tabs.titles.overview')</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="overview" role="tabpanel" aria-expanded="true">
                        <div class="col">
                            <div class="table-responsive">
                                <table class="table table-hover">

                                    <tr>
                                        <th>@lang('labels.backend.access.users.tabs.content.overview.name')</th>
                                        <td>{{ $user->name }}</td>
                                    </tr>

                                    <tr>
                                        <th>手机号</th>
                                        <td>{{ $user->mobile }}</td>
                                    </tr>

                                    <tr>
                                        <th>@lang('labels.backend.access.users.tabs.content.overview.status')</th>
                                        <td>{!! $user->status_label !!}</td>
                                    </tr>


                                    <tr>
                                        <th>@lang('labels.backend.access.users.tabs.content.overview.last_login_at')</th>
                                        <td>
                                            @if($user->last_login_at)
                                                {{ $user->last_login_at }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>@lang('labels.backend.access.users.tabs.content.overview.last_login_ip')</th>
                                        <td>{{ $user->last_login_ip ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div><!--table-responsive-->
                    </div><!--tab-->
                </div><!--tab-content-->
            </div><!--col-->
        </div><!--row-->
    </div><!--card-body-->

    <div class="card-footer">
        <div class="row">
            <div class="col">
                <small class="float-right text-muted">
                    <strong>@lang('labels.backend.access.users.tabs.content.overview.created_at'):</strong> {{ $user->created_at }} ({{ $user->created_at->diffForHumans() }}),
                    <strong>@lang('labels.backend.access.users.tabs.content.overview.last_updated'):</strong> {{ $user->updated_at }} ({{ $user->updated_at->diffForHumans() }})

                </small>
            </div><!--col-->
        </div><!--row-->
    </div><!--card-footer-->
</div><!--card-->
@endsection
