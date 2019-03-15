@extends('backend.layouts.app')

@section('title', '版本管理 | 添加版本')


@section('content')
{{ html()->form('POST', route('admin.version.store'))->class('form-horizontal')->open() }}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-5">
                    <h4 class="card-title mb-0">
                        版本管理
                        <small class="text-muted">添加版本</small>
                    </h4>
                </div><!--col-->
            </div><!--row-->

            <hr>

            <div class="row mt-4">
                <div class="col">
                    <div class="form-group row">
                        {{ html()->label('版本号')
                            ->class('col-md-2 form-control-label')
                            ->for('app_version') }}

                        <div class="col-md-10">
                            {{ html()->text('app_version')
                                ->class('form-control')
                                ->placeholder('3位APP版本号,如:1.0.0')
                                ->attribute('maxlength', 191)
                                ->required()
                                ->autofocus() }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('下载地址')
                            ->class('col-md-2 form-control-label')
                            ->for('package_url') }}

                        <div class="col-md-10">
                            <div class="input-group">
                                {{ html()->text('package_url')
                                ->class('form-control')
                                ->placeholder('包的更新地址,以.wgt结尾')
                                ->attribute('maxlength', 191)
                                ->required()
                            }}
                            </div>
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('是否ios强更')->class('col-md-2 form-control-label')->for('is_ios_force') }}

                        <div class="col-md-10">

                            <div class="form-check form-check-inline mr-1">
                                <input class="form-check-input" type="radio" value="0" checked name="is_ios_force">
                                <label class="form-check-label" for="inline-radio1">不强更(热更新,用户无感知)</label>
                            </div>
                            <div class="form-check form-check-inline mr-1">
                                <input class="form-check-input" type="radio" value="1" name="is_ios_force">
                                <label class="form-check-label" for="inline-radio2">强更(需要强制跳转到App store下载)</label>
                            </div>
                            <div class="form-check form-check-inline mr-1">
                                <input class="form-check-input" type="radio" value="2" name="is_ios_force">
                                <label class="form-check-label" for="inline-radio2">不更新</label>
                            </div>
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('是否android强更')->class('col-md-2 form-control-label')->for('is_android_force') }}

                        <div class="col-md-10">

                            <div class="form-check form-check-inline mr-1">
                                <input class="form-check-input" type="radio" value="0" checked name="is_android_force">
                                <label class="form-check-label" for="inline-radio1">不强更(热更新,用户无感知)</label>
                            </div>
                            <div class="form-check form-check-inline mr-1">
                                <input class="form-check-input" type="radio" value="1" name="is_android_force">
                                <label class="form-check-label" for="inline-radio2">强更(需要强制跳转到安卓市场下载)</label>
                            </div>
                            <div class="form-check form-check-inline mr-1">
                                <input class="form-check-input" type="radio" value="2" name="is_android_force">
                                <label class="form-check-label" for="inline-radio2">不更新</label>
                            </div>
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('更新日志')
                            ->class('col-md-2 form-control-label')
                            ->for('update_msg') }}

                        <div class="col-md-10">
                            {{ html()->textarea('update_msg')
                                ->class('form-control')
                                ->attribute('rows',9)
                                ->placeholder('更新日志')
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
                    {{ form_cancel(route('admin.version.index'), __('buttons.general.cancel')) }}
                </div><!--col-->

                <div class="col text-right">
                    {{ form_submit(__('buttons.general.crud.create')) }}
                </div><!--col-->
            </div><!--row-->
        </div><!--card-footer-->
    </div><!--card-->
{{ html()->form()->close() }}
@endsection
