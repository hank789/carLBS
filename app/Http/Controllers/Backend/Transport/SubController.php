<?php

namespace App\Http\Controllers\Backend\Transport;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Transport\ManageMainRequest;
use App\Http\Requests\Backend\Transport\StoreMainRequest;
use App\Models\Auth\ApiUser;
use App\Models\Transport\TransportMain;
use App\Models\Transport\TransportSub;
use App\Services\NumberUuid;

/**
 * Class UserController.
 */
class SubController extends Controller
{
    /**
     * @param ManageMainRequest $request
     * @param User              $user
     *
     * @return mixed
     */
    public function show(ManageMainRequest $request, $id)
    {
        $main = TransportSub::find($id);
        return view('backend.transport.main.show')
            ->with('main',$main);
    }
}
