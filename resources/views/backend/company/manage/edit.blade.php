@extends('backend.layouts.app')

@section('title', '公司管理 | 修改公司')

@section('content')
    {{ html()->form('PATCH', route('admin.company.manage.update',['id'=>$company->id]))->class('form-horizontal')->open() }}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-5">
                    <h4 class="card-title mb-0">
                        公司管理
                        <small class="text-muted">修改公司</small>
                    </h4>
                </div><!--col-->
            </div><!--row-->

            <hr>

            <div class="row mt-4">
                <div class="col">
                    <div class="form-group row">
                        {{ html()->label('公司名称')
                            ->class('col-md-2 form-control-label')
                            ->for('company_name') }}

                        <div class="col-md-10">
                            {{ html()->text('company_name')
                                ->class('form-control')
                                ->placeholder('公司名称')
                                ->attribute('maxlength', 191)
                                ->required()
                                ->value($company->company_name)
                                ->autofocus() }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label(__('validation.attributes.backend.access.users.active'))->class('col-md-2 form-control-label')->for('active') }}

                        <div class="col-md-10">
                            <label class="switch switch-label switch-pill switch-primary">
                                {{ html()->checkbox('active', $company->status==1?true:false)->class('switch-input') }}
                                <span class="switch-slider" data-checked="是" data-unchecked="否"></span>
                            </label>
                        </div><!--col-->
                    </div><!--form-group-->

                </div><!--col-->
            </div><!--row-->
        </div><!--card-body-->

        <div class="card-footer">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.company.manage.index'), __('buttons.general.cancel')) }}
                </div><!--col-->

                <div class="col text-right">
                    {{ form_submit('提交修改') }}
                </div><!--col-->
            </div><!--row-->
        </div><!--card-footer-->
    </div><!--card-->
    {{ html()->form()->close() }}
@endsection
