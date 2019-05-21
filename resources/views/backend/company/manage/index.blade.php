@extends('backend.layouts.app')

@section('title', app_display_name() . ' | 公司管理')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-5">
                <h4 class="card-title mb-0">
                    公司管理 <small class="text-muted">管理客户公司信息</small>
                </h4>
            </div><!--col-->
            <div class="col-sm-7">
                <form name="searchForm" class="form-horizontal" action="{{ route('admin.company.manage.index') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="filter" value="{{ $filter['filter']??'' }}">
                    <div class="form-group row">
                        <div class="col-9">
                            <input type="text" class="form-control" name="company_name" placeholder="公司名称" value="{{ $filter['company_name']??'' }}"/>
                        </div>

                        <div class="col-3">
                            <button type="submit" class="btn btn-primary">搜索</button>
                            <a href="{{ route('admin.company.manage.create') }}" class="btn btn-success ml-1" data-toggle="tooltip" title="@lang('labels.general.create_new')"><i class="fas fa-plus-circle"></i></a>
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
                            <th>名称</th>
                            <th>人数</th>
                            <th>App</th>
                            <th>添加时间</th>
                            <th>状态</th>
                            <th>@lang('labels.general.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($companies as $company)
                            <tr>
                                <td>{{ $company->company_name }}</td>
                                <td>{{ $company->countUsers() }}</td>
                                <td>{{ $company->getAppname() }}</td>
                                <td>{{ ($company->created_at) }}</td>
                                <td>{!! $company->status_label !!}</td>
                                <td>
                                    <div class="btn-group-xs" >
                                        <a class="btn btn-default" href="{{ route('admin.company.manage.edit',['id'=>$company->id]) }}" data-toggle="tooltip" title="编辑"><i class="fa fa-edit"></i></a>
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
                    <span class="total-num">共 {{ $companies->total() }} 条数据</span>
                    {!! str_replace('/?', '?', $companies->appends($filter)->render()) !!}
                </div>
            </div><!--col-->
        </div><!--row-->
    </div><!--card-body-->
</div><!--card-->
@endsection

@section('script')
    <script type="text/javascript">

    </script>
@endsection
