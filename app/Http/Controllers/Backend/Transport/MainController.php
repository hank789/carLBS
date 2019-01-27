<?php

namespace App\Http\Controllers\Backend\Transport;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Transport\ManageUserRequest;
use App\Models\Auth\ApiUser;
use App\Models\Transport\TransportMain;

/**
 * Class UserController.
 */
class MainController extends Controller
{

    /**
     * @param ManageUserRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(ManageUserRequest $request)
    {
        $filter =  $request->all();
        $query = TransportMain::query();
        if (isset($filter['filter'])) {
            switch ($filter['filter']) {
                case 'pending':
                    $query = $query->where('transport_status',TransportMain::TRANSPORT_STATUS_PENDING);
                    break;
                case 'processing':
                    $query = $query->where('transport_status',TransportMain::TRANSPORT_STATUS_PROCESSING);
                    break;
                case 'finished':
                    $query = $query->where('transport_status',TransportMain::TRANSPORT_STATUS_FINISH);
                    break;
                case 'canceled':
                    $query = $query->where('transport_status',TransportMain::TRANSPORT_STATUS_CANCEL);
                    break;
                case 'all':
                    break;
                default:
                    break;
            }
        }
        if (isset($filter['transport_number']) && $filter['transport_number']) {
            $query = $query->where('transport_number','like','%'.$filter['transport_number'].'%');
        }
        if (isset($filter['transport_end_place']) && $filter['transport_end_place']) {
            $query = $query->where('transport_end_place','like','%'.$filter['transport_end_place'].'%');
        }
        $list = $query->orderBy('id','desc')->paginate(config('backend.page_size'));
        return view('backend.transport.main.index')
            ->with('list',$list)->with('filter',$filter);
    }

    /**
     * @param ManageUserRequest $request
     * @param User              $user
     *
     * @return mixed
     */
    public function show(ManageUserRequest $request, ApiUser $user)
    {
        return view('backend.transport.user.show')
            ->withUser($user);
    }


    public function mark(ManageUserRequest $request, $id, $status)
    {
        $user = ApiUser::find($id);
        if (auth()->id() == $user->id && $status == 0) {
            throw new GeneralException(__('exceptions.backend.access.users.cant_deactivate_self'));
        }

        $user->status = $status;

        switch ($status) {
            case 0:
                event(new UserDeactivated($user));
                break;

            case 1:
                event(new UserReactivated($user));
                break;
        }

        if ($user->save()) {
            return $user;
        }

        return redirect()->route(url()->previous())->withFlashSuccess(__('alerts.backend.users.updated'));
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
        return view('backend.auth.user.create')
            ->withRoles($roleRepository->with('permissions')->get(['id', 'name']))
            ->withPermissions($permissionRepository->get(['id', 'name']));
    }

    /**
     * @param StoreUserRequest $request
     *
     * @return mixed
     * @throws \Throwable
     */
    public function store(StoreUserRequest $request)
    {
        $this->userRepository->create($request->only(
            'first_name',
            'last_name',
            'email',
            'password',
            'active',
            'confirmed',
            'confirmation_email',
            'roles',
            'permissions'
        ));

        return redirect()->route('admin.auth.user.index')->withFlashSuccess(__('alerts.backend.users.created'));
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
        return view('backend.auth.user.edit')
            ->withUser($user)
            ->withRoles($roleRepository->get())
            ->withUserRoles($user->roles->pluck('name')->all())
            ->withPermissions($permissionRepository->get(['id', 'name']))
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
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userRepository->update($user, $request->only(
            'first_name',
            'last_name',
            'email',
            'roles',
            'permissions'
        ));

        return redirect()->route('admin.auth.user.index')->withFlashSuccess(__('alerts.backend.users.updated'));
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

        return redirect()->route('admin.auth.user.deleted')->withFlashSuccess(__('alerts.backend.users.deleted'));
    }
}
