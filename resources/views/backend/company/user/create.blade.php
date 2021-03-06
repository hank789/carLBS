@extends('backend.layouts.app')

@section('title', __('labels.backend.access.users.management') . ' | ' . __('labels.backend.access.users.create'))

@section('breadcrumb-links')
    @include('backend.company.user.includes.breadcrumb-links')
@endsection

@section('head-script')
    {{ style(('js/plugins/select2/css/select2.min.css'),[],config('app.use_ssl')) }}
    {{ style(('js/plugins/select2/css/select2-bootstrap.min.css'),[],config('app.use_ssl')) }}
@endsection

@section('content')
    {{ html()->form('POST', route('admin.company.user.store'))->class('form-horizontal')->open() }}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-5">
                        <h4 class="card-title mb-0">
                            @lang('labels.backend.access.users.management')
                            <small class="text-muted">@lang('labels.backend.access.users.create')</small>
                        </h4>
                    </div><!--col-->
                </div><!--row-->

                <hr>

                <div class="row mt-4 mb-4">
                    <div class="col">
                        <div class="form-group row">
                        {{ html()->label(__('validation.attributes.backend.access.users.last_name'))->class('col-md-2 form-control-label')->for('last_name') }}

                            <div class="col-md-10">
                                {{ html()->text('last_name')
                                    ->class('form-control')
                                    ->placeholder(__('validation.attributes.backend.access.users.last_name'))
                                    ->attribute('maxlength', 191)
                                    ->required() }}
                            </div><!--col-->
                        </div><!--form-group-->

                        <div class="form-group row">
                            {{ html()->label(__('validation.attributes.backend.access.users.first_name'))->class('col-md-2 form-control-label')->for('first_name') }}

                            <div class="col-md-10">
                                {{ html()->text('first_name')
                                    ->class('form-control')
                                    ->placeholder(__('validation.attributes.backend.access.users.first_name'))
                                    ->attribute('maxlength', 191)
                                    ->required()
                                    ->autofocus() }}
                            </div><!--col-->
                        </div><!--form-group-->

                        <div class="form-group row">
                            {{ html()->label('手机号')->class('col-md-2 form-control-label')->for('mobile') }}

                            <div class="col-md-10">
                                {{ html()->text('mobile')
                                    ->class('form-control')
                                    ->placeholder('手机号')
                                    ->attribute('maxlength', 191)
                                    ->required() }}
                            </div><!--col-->
                        </div><!--form-group-->

                        <div class="form-group row">
                            {{ html()->label(__('validation.attributes.backend.access.users.active'))->class('col-md-2 form-control-label')->for('active') }}

                            <div class="col-md-10">
                                <label class="switch switch-label switch-pill switch-primary">
                                    {{ html()->checkbox('active', true, '1')->class('switch-input') }}
                                    <span class="switch-slider" data-checked="是" data-unchecked="否"></span>
                                </label>
                            </div><!--col-->
                        </div><!--form-group-->

                        <div class="form-group row" id="select_company_div" style="{{ $userCompany->company_type == 2?'display:none':'' }}">
                            {{ html()->label('公司')->class('col-md-2 form-control-label')->for('company_id') }}

                            <div class="col-md-10">
                                <select id="company_id" name="company_id" class="form-control">
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ $userCompany->id == $company->id?'selected':'' }}>{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row" style="{{ $userCompany->company_type == 2?'display:none':'' }}">
                            {{ html()->label('是否供应商人员')->class('col-md-2 form-control-label')->for('is_vendor') }}

                            <div class="col-md-10">
                                <label class="switch switch-label switch-pill switch-primary">
                                    {{ html()->checkbox('is_vendor', $userCompany->company_type == 2?true:false, '1')->class('switch-input') }}
                                    <span class="switch-slider" data-checked="是" data-unchecked="否"></span>
                                </label>
                            </div><!--col-->
                        </div><!--form-group-->

                        <div class="form-group row" id="select_vendor_company_div" style="{{ $userCompany->company_type == 1?'display:none':'' }}">
                            {{ html()->label('供应商')->class('col-md-2 form-control-label')->for('vendor_company_id') }}

                                <div class="col-md-10">
                                    <select id="vendor_company_id" name="vendor_company_id" class="form-control">
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                        </div>

                        <div class="form-group row" style="display: none">
                            {{ html()->label(__('validation.attributes.backend.access.users.confirmed'))->class('col-md-2 form-control-label')->for('confirmed') }}

                            <div class="col-md-10">
                                <label class="switch switch-label switch-pill switch-primary">
                                    {{ html()->checkbox('confirmed', true, '1')->class('switch-input') }}
                                    <span class="switch-slider" data-checked="是" data-unchecked="否"></span>
                                </label>
                            </div><!--col-->
                        </div><!--form-group-->

                        @if(! config('access.users.requires_approval') && false)
                            <div class="form-group row">
                                {{ html()->label(__('validation.attributes.backend.access.users.send_confirmation_email') . '<br/>' . '<small>' .  __('strings.backend.access.users.if_confirmed_off') . '</small>')->class('col-md-2 form-control-label')->for('confirmation_email') }}

                                <div class="col-md-10">
                                    <label class="switch switch-label switch-pill switch-primary">
                                        {{ html()->checkbox('confirmation_email', true, '1')->class('switch-input') }}
                                        <span class="switch-slider" data-checked="是" data-unchecked="否"></span>
                                    </label>
                                </div><!--col-->
                            </div><!--form-group-->
                        @endif

                        <div class="form-group row">
                            {{ html()->label('权限设置')->class('col-md-2 form-control-label') }}

                            <div class="col-md-10">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>@lang('labels.backend.access.users.table.roles')</th>
                                            <th>@lang('labels.backend.access.users.table.permissions')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                @if($roles->count())
                                                    @foreach($roles as $role)
                                                        @if ($role->id !=1)
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <div class="checkbox d-flex align-items-center">
                                                                        {{ html()->label(
                                                                                html()->checkbox('roles[]', old('roles') && in_array($role->name, old('roles')) ? true : false, $role->name)
                                                                                      ->class('switch-input')
                                                                                      ->id('role-'.$role->id)
                                                                                . '<span class="switch-slider" data-checked="是" data-unchecked="否"></span>')
                                                                            ->class('switch switch-label switch-pill switch-primary mr-2')
                                                                            ->for('role-'.$role->id) }}
                                                                        {{ html()->label(ucwords($role->name))->for('role-'.$role->id) }}
                                                                    </div>
                                                                </div>
                                                                <div class="card-body">
                                                                    @if($role->id != 1)
                                                                        @if($role->permissions->count())
                                                                            @foreach($role->permissions as $permission)
                                                                                <i class="fas fa-dot-circle"></i> {{ ucwords($permission->name) }}
                                                                            @endforeach
                                                                        @else
                                                                            @lang('labels.general.none')
                                                                        @endif
                                                                    @else
                                                                        @lang('labels.backend.access.users.all_permissions')
                                                                    @endif
                                                                </div>
                                                            </div><!--card-->
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if($permissions->count())
                                                    @foreach($permissions as $permission)
                                                        <div class="checkbox d-flex align-items-center">
                                                            {{ html()->label(
                                                                    html()->checkbox('permissions[]', old('permissions') && in_array($permission->name, old('permissions')) ? true : false, $permission->name)
                                                                          ->class('switch-input')
                                                                          ->id('permission-'.$permission->id)
                                                                        . '<span class="switch-slider" data-checked="是" data-unchecked="否"></span>')
                                                                    ->class('switch switch-label switch-pill switch-primary mr-2')
                                                                ->for('permission-'.$permission->id) }}
                                                            {{ html()->label(ucwords($permission->name))->for('permission-'.$permission->id) }}
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div><!--col-->
                        </div><!--form-group-->
                    </div><!--col-->
                </div><!--row-->
            </div><!--card-body-->

            <div class="card-footer clearfix">
                <div class="row">
                    <div class="col">
                        {{ form_cancel(route('admin.company.user.index'), __('buttons.general.cancel')) }}
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
    {!! script(('js/plugins/select2/js/select2.min.js'),[],config('app.use_ssl')) !!}
    <script type="text/javascript">
        $(function(){
            $("#vendor_company_id").select2({
                theme:'bootstrap',
                placeholder: "选择供应商",
                tags:false
            });
            $('#is_vendor').change(function() {
                var ischecked = $('#is_vendor').prop('checked');
                if (ischecked) {
                    //是供应商
                    $('#select_vendor_company_div').css('display','');
                    $('#select_company_div').css('display','none');
                } else {
                    $('#select_vendor_company_div').css('display','none');
                    $('#select_company_div').css('display','');
                }
            });
        });
    </script>
@endsection
