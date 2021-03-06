@extends('backend.layouts.app')

@section('title', '版本管理 | 修改版本')


@section('content')
    {{ html()->form('PATCH', route('admin.version.update',['id'=>$version->id]))->class('form-horizontal')->open() }}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-5">
                    <h4 class="card-title mb-0">
                        版本管理
                        <small class="text-muted">修改版本</small>
                    </h4>
                </div><!--col-->
            </div><!--row-->

            <hr>

            <div class="row mt-4">
                <div class="col">

                    <div class="form-group row" id="select_vendor_company_div">
                        {{ html()->label('App')->class('col-md-2 form-control-label')->for('app_name') }}

                        <div class="col-md-10">
                            <select id="app_name" name="app_name" class="form-control">
                                @foreach($appNames as $name)
                                    <option value="{{ $name['key'] }}" {{ $version->app_name == $name['key']?'selected':'' }}>{{ $name['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

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
                                ->value($version->app_version)
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
                                ->value($version->package_url)
                                ->required()
                            }}
                            </div>
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('是否ios强更')->class('col-md-2 form-control-label')->for('is_ios_force') }}

                        <div class="col-md-10">

                            <div class="form-check form-check-inline mr-1">
                                <input class="form-check-input" type="radio" value="0" @if ( $version->is_ios_force == 0) checked @endif name="is_ios_force">
                                <label class="form-check-label" for="inline-radio1">不强更(热更新,用户无感知)</label>
                            </div>
                            <div class="form-check form-check-inline mr-1">
                                <input class="form-check-input" type="radio" value="1" name="is_ios_force" @if ( $version->is_ios_force == 1) checked @endif>
                                <label class="form-check-label" for="inline-radio2">强更(需要强制跳转到App store下载)</label>
                            </div>
                            <div class="form-check form-check-inline mr-1">
                                <input class="form-check-input" type="radio" value="2" name="is_ios_force" @if ( $version->is_ios_force == 2) checked @endif>
                                <label class="form-check-label" for="inline-radio2">不更新</label>
                            </div>
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('是否android强更')->class('col-md-2 form-control-label')->for('is_android_force') }}

                        <div class="col-md-10">

                            <div class="form-check form-check-inline mr-1">
                                <input class="form-check-input" type="radio" value="0" name="is_android_force" @if ( $version->is_android_force == 0) checked @endif>
                                <label class="form-check-label" for="inline-radio1">不强更(热更新,用户无感知)</label>
                            </div>
                            <div class="form-check form-check-inline mr-1">
                                <input class="form-check-input" type="radio" value="1" name="is_android_force" @if ( $version->is_android_force == 1) checked @endif>
                                <label class="form-check-label" for="inline-radio2">强更(需要强制跳转到安卓市场下载)</label>
                            </div>
                            <div class="form-check form-check-inline mr-1">
                                <input class="form-check-input" type="radio" value="2" name="is_android_force" @if ( $version->is_android_force == 2) checked @endif>
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
                                ->value($version->update_msg)
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
                    {{ form_submit('提交修改') }}
                </div><!--col-->
            </div><!--row-->
        </div><!--card-footer-->
    </div><!--card-->
    {{ html()->form()->close() }}
@endsection
