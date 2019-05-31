<?php

namespace App\Http\Controllers\Backend\Transport;

use App\Events\Backend\Transport\User\UserDeactivated;
use App\Events\Backend\Transport\User\UserReactivated;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Transport\ManageUserRequest;
use App\Models\Auth\ApiUser;
use App\Models\Transport\TransportEntity;

/**
 * Class UserController.
 */
class CarController extends Controller
{

    /**
     * @param ManageUserRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(ManageUserRequest $request)
    {
        $filter =  $request->all();
        $query = TransportEntity::query();

        if (isset($filter['car_number']) && $filter['car_number']) {
            $query = $query->where('car_number','like','%'.$filter['car_number'].'%');
        }
        $cars = $query->orderBy('id','desc')->paginate(config('backend.page_size'));
        return view('backend.transport.car.index')
            ->with('cars',$cars)->with('filter',$filter);
    }
}
