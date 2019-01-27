<?php

namespace App\Http\Controllers\Backend\Transport;

use App\Events\Backend\Transport\User\UserDeactivated;
use App\Events\Backend\Transport\User\UserReactivated;
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
        $filter =  $request->all();
        $query = ApiUser::query();
        if (isset($filter['filter'])) {
            switch ($filter['filter']) {
                case 'active':
                    $query = $query->where('status',1);
                    break;
                case 'deactivated':
                    $query = $query->where('status',0);
                    break;
                case 'all':
                    break;
                default:
                    break;
            }
        }
        if (isset($filter['nameOrMobile']) && $filter['nameOrMobile']) {
            if (is_numeric($filter['nameOrMobile'])) {
                $query = $query->where('mobile','like','%'.$filter['nameOrMobile'].'%');
            } else {
                $query = $query->where('name','like','%'.$filter['nameOrMobile'].'%');
            }
        }
        $users = $query->orderBy('id','desc')->paginate(config('backend.page_size'));
        return view('backend.transport.user.index')
            ->withUsers($users)->with('filter',$filter);
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
}
