<?php

namespace App\Http\Controllers\Backend\Transport;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Transport\ManageUserRequest;
use App\Models\Auth\ApiUser;

/**
 * Class UserController.
 */
class UserController extends Controller
{

    /**
     * @param ManageUserRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(ManageUserRequest $request)
    {
        $query = ApiUser::query();
        $users = $query->where('status',1)->orderBy('id','desc')->paginate(config('backend.page_size'));
        return view('backend.transport.user.index')
            ->withUsers($users);
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

    /**
     * @param ManageUserRequest $request
     *
     * @return mixed
     */
    public function getDeactivated(ManageUserRequest $request)
    {
        return view('backend.transport.user.deactivated')
            ->withUsers($this->userRepository->getInactivePaginated(25, 'id', 'asc'));
    }

    /**
     * @param ManageUserRequest $request
     * @param User              $user
     * @param                   $status
     *
     * @return mixed
     * @throws \App\Exceptions\GeneralException
     */
    public function mark(ManageUserRequest $request, ApiUser $user, $status)
    {
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

        return redirect()->route(
            $status == 1 ?
                'admin.transport.user.index' :
                'admin.transport.user.deactivated'
        )->withFlashSuccess(__('alerts.backend.users.updated'));
    }
}
