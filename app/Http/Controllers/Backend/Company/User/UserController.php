<?php

namespace App\Http\Controllers\Backend\Company\User;

use App\Exceptions\GeneralException;
use App\Models\Auth\Company;
use App\Models\Auth\CompanyRel;
use App\Models\Auth\User;
use App\Http\Controllers\Controller;
use App\Events\Backend\Auth\User\UserDeleted;
use App\Repositories\Backend\Auth\RoleRepository;
use App\Repositories\Backend\Auth\UserRepository;
use App\Repositories\Backend\Auth\PermissionRepository;
use App\Http\Requests\Backend\Auth\Company\ManageUserRequest;

/**
 * Class UserController.
 */
class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param ManageUserRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(ManageUserRequest $request)
    {
        $user = $request->user();
        $query = User::with('roles', 'permissions', 'providers')
            ->active()
            ->where('id','!=',1);
        if ($user->company_id != 1) {
            $companyIds = CompanyRel::where('company_id',$user->company_id)->pluck('vendor_id')->toArray();
            $companyIds[] = $user->company_id;
            $query = $query->whereIn('company_id',$companyIds);
        }

        $users = $query->orderBy('id', 'desc')->paginate(25);
        return view('backend.company.user.index')
            ->withUsers($users);
    }

    /**
     * @param ManageUserRequest    $request
     * @param RoleRepository       $roleRepository
     * @param PermissionRepository $permissionRepository
     *
     * @return mixed
     */
    public function create(ManageUserRequest $request, RoleRepository $roleRepository, PermissionRepository $permissionRepository)
    {
        $user = $request->user();
        $userCompany = $user->company;
        $vendors = [];
        if ($userCompany->company_type == Company::COMPANY_TYPE_MAIN) {
            $vendors = CompanyRel::where('company_id',$userCompany->id)->get();
            $roles = $roleRepository->with('permissions')->get(['id', 'name']);
            $permissions = $permissionRepository->get(['id', 'name']);
        } else {
            $vendors = CompanyRel::where('vendor_id',$user->company_id)->get();
            $roles = $roleRepository->whereIn('name',['供应商人员','User'])->with('permissions')->get(['id', 'name']);
            $permissions = $permissionRepository->whereIn('name',['后台登陆','行程管理','在线车辆'])->get(['id', 'name']);
        }
        if ($user->company_id == 1) {
            $companies = Company::where('company_type',Company::COMPANY_TYPE_MAIN)->get();
        } else {
            $companies = Company::where('company_type',Company::COMPANY_TYPE_MAIN)->where('id',$user->company_id)->get();
        }

        return view('backend.company.user.create')
            ->with('userCompany',$userCompany)
            ->with('companies',$companies)
            ->with('vendors',$vendors)
            ->withRoles($roles)
            ->withPermissions($permissions);
    }

    /**
     * @param ManageUserRequest $request
     *
     * @return mixed
     * @throws \Throwable
     */
    public function store(ManageUserRequest $request)
    {
        $newUser = $this->userRepository->create($request->only(
            'first_name',
            'last_name',
            'mobile',
            'password',
            'active',
            'confirmed',
            'confirmation_email',
            'roles',
            'permissions'
        ));
        $newUser->company_id = $request->input('company_id');
        $is_vendor = $request->input('is_vendor',0);
        if ($is_vendor) {
            $vendor_company_id = $request->input('vendor_company_id');
            $vendor = Company::find($vendor_company_id);
            if (!$vendor) {
                throw new GeneralException('该供应商不存在');
            }
            $newUser->company_id = $vendor->id;
        }
        $newUser->save();
        return redirect()->route('admin.company.user.index')->withFlashSuccess(__('alerts.backend.users.created'));
    }

    /**
     * @param ManageUserRequest $request
     * @param User              $user
     *
     * @return mixed
     */
    public function show(ManageUserRequest $request, User $user)
    {
        return view('backend.company.user.show')
            ->withUser($user);
    }

    /**
     * @param ManageUserRequest    $request
     * @param RoleRepository       $roleRepository
     * @param PermissionRepository $permissionRepository
     * @param User                 $user
     *
     * @return mixed
     */
    public function edit(ManageUserRequest $request, RoleRepository $roleRepository, PermissionRepository $permissionRepository, User $user)
    {
        $loginUser = $request->user();
        $userCompany = $loginUser->company;
        $vendors = [];
        if ($userCompany->company_type == Company::COMPANY_TYPE_MAIN) {
            $vendors = CompanyRel::where('company_id',$userCompany->id)->get();
            $roles = $roleRepository->with('permissions')->get(['id', 'name']);
            $permissions = $permissionRepository->get(['id', 'name']);
        } else {
            $vendors = CompanyRel::where('vendor_id',$user->company_id)->get();
            $roles = $roleRepository->whereIn('name',['供应商人员','User'])->with('permissions')->get(['id', 'name']);
            $permissions = $permissionRepository->whereIn('name',['后台登陆','行程管理','在线车辆'])->get(['id', 'name']);
        }

        if ($loginUser->company_id == 1) {
            $companies = Company::where('company_type',Company::COMPANY_TYPE_MAIN)->get();
        } else {
            $companies = Company::where('company_type',Company::COMPANY_TYPE_MAIN)->where('id',$loginUser->company_id)->get();
        }

        return view('backend.company.user.edit')
            ->withUser($user)
            ->with('companies',$companies)
            ->with('userCompany',$userCompany)
            ->with('vendors',$vendors)
            ->withRoles($roles)
            ->withUserRoles($user->roles->pluck('name')->all())
            ->withPermissions($permissions)
            ->withUserPermissions($user->permissions->pluck('name')->all());
    }

    /**
     * @param UpdateUserRequest $request
     * @param User              $user
     *
     * @return mixed
     * @throws \App\Exceptions\GeneralException
     * @throws \Throwable
     */
    public function update(ManageUserRequest $request, User $user)
    {
        $loginUser = $request->user();
        $newUser = $this->userRepository->update($user, $request->only(
            'first_name',
            'last_name',
            'mobile',
            'roles',
            'permissions'
        ));

        $is_vendor = $request->input('is_vendor',0);
        $newUser->company_id = $request->input('company_id');
        if ($is_vendor) {
            $vendor_company_id = $request->input('vendor_company_id');
            $vendor = Company::find($vendor_company_id);
            if (!$vendor) {
                throw new GeneralException('该供应商不存在');
            }
            $newUser->company_id = $vendor->id;
        }
        $newUser->save();

        return redirect()->route('admin.company.user.index')->withFlashSuccess(__('alerts.backend.users.updated'));
    }

    /**
     * @param ManageUserRequest $request
     * @param User              $user
     *
     * @return mixed
     * @throws \Exception
     */
    public function destroy(ManageUserRequest $request, User $user)
    {
        $this->userRepository->deleteById($user->id);

        event(new UserDeleted($user));

        return redirect()->route('admin.company.user.deleted')->withFlashSuccess(__('alerts.backend.users.deleted'));
    }
}
