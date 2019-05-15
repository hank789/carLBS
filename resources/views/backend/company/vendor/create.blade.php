@extends('backend.layouts.app')

@section('title', '供应商管理 | 添加供应商')

@section('content')
{{ html()->form('POST', route('admin.company.vendor.store'))->class('form-horizontal')->open() }}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-5">
                    <h4 class="card-title mb-0">
                        供应商管理
                        <small class="text-muted">添加供应商</small>
                    </h4>
                </div><!--col-->
            </div><!--row-->

            <hr>

            <div class="row mt-4">
                <div class="col">
                    <div class="form-group row">
                        {{ html()->label('供应商名称')
                            ->class('col-md-2 form-control-label')
                            ->for('company_name') }}

                        <div class="col-md-10">
                            {{ html()->text('company_name')
                                ->class('form-control')
                                ->placeholder('供应商名称')
                                ->attribute('maxlength', 191)
                                ->required()
                                ->autofocus() }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('所属公司')
                            ->class('col-md-2 form-control-label')
                            ->for('company_id') }}

                        <div class="col-md-10">
                            <div class="input-group">
                                {{ html()->text('company_id')
                                ->class('form-control')
                                ->placeholder('')
                                ->attribute('maxlength', 191)
                                ->value($company->company_name)
                                ->readonly()
                            }}
                            </div>
                        </div><!--col-->
                    </div><!--form-group-->

                </div><!--col-->
            </div><!--row-->
        </div><!--card-body-->

        <div class="card-footer">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.company.vendor.index'), __('buttons.general.cancel')) }}
                </div><!--col-->

                <div class="col text-right">
                    {{ form_submit(__('buttons.general.crud.create')) }}
                </div><!--col-->
            </div><!--row-->
        </div><!--card-footer-->
    </div><!--card-->
{{ html()->form()->close() }}
@endsection
