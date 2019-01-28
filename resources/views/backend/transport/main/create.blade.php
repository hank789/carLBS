@extends('backend.layouts.app')

@section('title', '行程管理 | 添加行程')

@section('content')
{{ html()->form('POST', route('admin.transport.main.store'))->class('form-horizontal')->open() }}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-5">
                    <h4 class="card-title mb-0">
                        行程管理
                        <small class="text-muted">添加行程</small>
                    </h4>
                </div><!--col-->
            </div><!--row-->

            <hr>

            <div class="row mt-4">
                <div class="col">
                    <div class="form-group row">
                        {{ html()->label('出发地')
                            ->class('col-md-2 form-control-label')
                            ->for('transport_start_place') }}

                        <div class="col-md-10">
                            {{ html()->text('transport_start_place')
                                ->class('form-control')
                                ->placeholder('车队出发地')
                                ->attribute('maxlength', 191)
                                ->required()
                                ->autofocus() }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('目的地')
                            ->class('col-md-2 form-control-label')
                            ->for('transport_end_place') }}

                        <div class="col-md-10">
                            {{ html()->text('transport_end_place')
                                ->class('form-control')
                                ->placeholder('车队目的地')
                                ->attribute('maxlength', 191)
                                ->required()
                            }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('联系人')
                            ->class('col-md-2 form-control-label')
                            ->for('transport_contact_people') }}

                        <div class="col-md-10">
                            {{ html()->text('transport_contact_people')
                                ->class('form-control')
                                ->placeholder('联系人')
                                ->attribute('maxlength', 191)
                                ->required()
                            }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('联系人电话')
                            ->class('col-md-2 form-control-label')
                            ->for('transport_contact_phone') }}

                        <div class="col-md-10">
                            {{ html()->text('transport_contact_phone')
                                ->class('form-control')
                                ->placeholder('联系人电话')
                                ->type('number')
                                ->required()
                            }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('出发时间')
                            ->class('col-md-2 form-control-label')
                            ->for('transport_start_time') }}

                        <div class="col-md-10">
                            {{ html()->text('transport_start_time')
                                ->class('form-control')
                                ->placeholder('出发时间')
                                ->required()
                            }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('状态')->class('col-md-2 form-control-label')->for('transport_status') }}

                        <div class="col-md-10">

                            <div class="form-check form-check-inline mr-1">
                                <input class="form-check-input" type="radio" value="0" name="transport_status">
                                <label class="form-check-label" for="inline-radio1">暂不发布</label>
                            </div>
                            <div class="form-check form-check-inline mr-1">
                                <input class="form-check-input" type="radio" value="1" checked name="transport_status">
                                <label class="form-check-label" for="inline-radio2">马上发布</label>
                            </div>
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('货物信息')
                            ->class('col-md-2 form-control-label')
                            ->for('transport_goods') }}

                        <div class="col-md-10">
                            {{ html()->textarea('transport_goods')
                                ->class('form-control')
                                ->placeholder('货物信息')
                                ->required()
                            }}
                        </div><!--col-->
                    </div><!--form-group-->

                </div><!--col-->
            </div><!--row-->
        </div><!--card-body-->

        <div class="card-footer">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.auth.role.index'), __('buttons.general.cancel')) }}
                </div><!--col-->

                <div class="col text-right">
                    {{ form_submit(__('buttons.general.crud.create')) }}
                </div><!--col-->
            </div><!--row-->
        </div><!--card-footer-->
    </div><!--card-->
{{ html()->form()->close() }}
@endsection
@section('script')
    <script type="text/javascript">
        /*daterange控件*/
        $('#transport_start_time').daterangepicker({
            timePicker: true,
            singleDatePicker: true,
            timePicker24Hour: true,
            locale: {
                applyLabel: '确认',
                cancelLabel: '取消',
                fromLabel: '从',
                toLabel: '到',
                weekLabel: '星期',
                customRangeLabel: '自定义范围',
                daysOfWeek: '日_一_二_三_四_五_六'.split('_'),
                monthNames: '1月_2月_3月_4月_5月_6月_7月_8月_9月_10月_11月_12月'.split('_'),
                firstDay: 1,
                format: 'YYYY-MM-DD HH:mm'
            }
        });
    </script>
@endsection