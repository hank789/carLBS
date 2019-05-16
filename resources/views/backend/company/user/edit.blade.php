@extends('backend.layouts.app')

@section('title', __('labels.backend.access.users.management') . ' | ' . __('labels.backend.access.users.edit'))

@section('breadcrumb-links')
    @include('backend.company.user.includes.breadcrumb-links')
@endsection

@section('head-script')
    {{ style(('js/plugins/select2/css/select2.min.css'),[],config('app.use_ssl')) }}
    {{ style(('js/plugins/select2/css/select2-bootstrap.min.css'),[],config('app.use_ssl')) }}
@endsection

@section('content')
{{ html()->modelForm($user, 'PATCH', route('admin.company.user.update', $user->id))->class('form-horizontal')->open() }}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-5">
                    <h4 class="card-title mb-0">
                        @lang('labels.backend.access.users.management')
                        <small class="text-muted">@lang('labels.backend.access.users.edit')</small>
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
                                ->required() }}
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

                    <div class="form-group row" id="select_company_div" style="{{ $user->company->company_type==2?'display:none':'' }}">
                        {{ html()->label('公司')->class('col-md-2 form-control-label')->for('company_id') }}

                        <div class="col-md-10">
                            <select id="company_id" name="company_id" class="form-control" {{ $userCompany->id != 1?'readonly':'' }}>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ $user->company_id == $company->id?'selected':'' }}>{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{ html()->label('是否供应商人员')->class('col-md-2 form-control-label')->for('is_vendor') }}

                        <div class="col-md-10">
                            <label class="switch switch-label switch-pill switch-primary">
                                {{ html()->checkbox('is_vendor', $user->company->company_type==2?true:false, '1')->class('switch-input') }}
                                <span class="switch-slider" data-checked="是" data-unchecked="否"></span>
                            </label>
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row" id="select_vendor_company_div" style="{{ $user->company->company_type==1?'display:none':'' }}">
                        {{ html()->label('供应商')->class('col-md-2 form-control-label')->for('vendor_company_id') }}

                        <div class="col-md-10">
                            <select id="vendor_company_id" name="vendor_company_id" class="form-control">
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->vendor_id }}" {{ $user->company_id == $vendor->vendor_id?'selected':'' }}>{{ $vendor->vendor->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{ html()->label('权限设置')->class('col-md-2 form-control-label') }}

                        <div class="table-responsive col-md-10">
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
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <div class="checkbox d-flex align-items-center">
                                                                {{ html()->label(
                                                                        html()->checkbox('roles[]', in_array($role->name, $userRoles), $role->name)
                                                                                ->class('switch-input')
                                                                                ->id('role-'.$role->id)
                                                                        . '<span class="switch-slider" data-checked="on" data-unchecked="off"></span>')
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
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            @if($permissions->count())
                                                @foreach($permissions as $permission)
                                                    <div class="checkbox d-flex align-items-center">
                                                        {{ html()->label(
                                                                html()->checkbox('permissions[]', in_array($permission->name, $userPermissions), $permission->name)
                                                                        ->class('switch-input')
                                                                        ->id('permission-'.$permission->id)
                                                                    . '<span class="switch-slider" data-checked="on" data-unchecked="off"></span>')
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
                        </div><!--col-->
                    </div><!--form-group-->
                </div><!--col-->
            </div><!--row-->
        </div><!--card-body-->

        <div class="card-footer">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.company.user.index'), __('buttons.general.cancel')) }}
                </div><!--col-->

                <div class="col text-right">
                    {{ form_submit(__('buttons.general.crud.update')) }}
                </div><!--row-->
            </div><!--row-->
        </div><!--card-footer-->
    </div><!--card-->
{{ html()->closeModelForm() }}
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