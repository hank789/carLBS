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
class MainController extends Controller
{

    /**
     * @param ManageMainRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(ManageMainRequest $request)
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
        /*时间过滤*/
        if( isset($filter['transport_start_time']) && $filter['transport_start_time'] ){
            $query->whereBetween('transport_start_time',explode(" - ",$filter['transport_start_time']));
        }
        $list = $query->orderBy('id','desc')->paginate(config('backend.page_size'));
        return view('backend.transport.main.index')
            ->with('list',$list)->with('filter',$filter);
    }

    /**
     * @param ManageMainRequest $request
     * @param User              $user
     *
     * @return mixed
     */
    public function show(ManageMainRequest $request, $id)
    {
        $main = TransportMain::find($id);
        return view('backend.transport.main.show')
            ->with('main',$main);
    }

    public function getSubList(ManageMainRequest $request, $id) {
        $draw = $request->input('draw',1);
        $start = $request->input('start',0);
        $length = $request->input('length',10);
        $page = $start/$length + 1;
        $list = TransportSub::where('transport_main_id',$id)->orderBy('id','desc')->paginate($length,['*'],'page',$page);
        $data = [];
        foreach ($list as $item) {
            $data[] = [
                $item->apiUser->name,
                $item->apiUser->mobile,
                $item->transportEntity->car_number,
                $item->transport_start_time,
                $item->getTransportEventCount(),
                $item->getTransportXiehuoCount(),
                $item->transport_goods['lastPosition']['formatted_address']??'',
                $item->status_label,
                $item->show_button
            ];
        }
        return response()->json([
            'draw' => $draw,
            'data' =>$data,
            'recordsTotal' => $list->total(),
            'recordsFiltered' => $list->total()
        ]);
    }


    public function mark(ManageMainRequest $request, $id, $status)
    {
        $main = TransportMain::find($id);

        \Log::info('test',[$id,$status]);
        $main->transport_status = $status;

        //todo 进行中的行程如果要取消，需要判断是否有在途的司机
        switch ($status) {
            case 0:
                break;
            case 1:
                break;
        }
        $main->save();

        return response('success');
    }

    /**
     * @param ManageMainRequest    $request
     *
     * @return mixed
     */
    public function create(ManageMainRequest $request)
    {
        return view('backend.transport.main.create');
    }

    /**
     * @param StoreMainRequest $request
     *
     * @return mixed
     * @throws \Throwable
     */
    public function store(StoreMainRequest $request)
    {
        $transportNumber = NumberUuid::instance()->get_uuid_number();
        $coordinate = coordinate_bd_decrypt($request->input('transport_end_place_longitude'),$request->input('transport_end_place_latitude'));
        TransportMain::create([
            'user_id' => $request->user()->id,
            'transport_number' => $transportNumber,
            'transport_start_place' => $request->input('transport_start_place'),
            'transport_end_place' => $request->input('transport_end_place'),
            'transport_contact_people' => $request->input('transport_contact_people'),
            'transport_contact_phone' => $request->input('transport_contact_phone'),
            'transport_start_time' => $request->input('transport_start_time'),
            'transport_goods' => [
                'transport_goods'=>$request->input('transport_goods'),
                'transport_end_place_longitude'=> $coordinate['gg_lon'],
                'transport_end_place_latitude'=> $coordinate['gg_lat'],
                'transport_end_place_coordsType' => 'gcj02',
            ],
            'transport_status' => $request->input('transport_status',TransportMain::TRANSPORT_STATUS_PROCESSING)
        ]);
        return redirect()->route('admin.transport.main.index')->withFlashSuccess('行程添加成功');
    }

    public function edit(ManageMainRequest $request,$id)
    {
        $main = TransportMain::find($id);
        return view('backend.transport.main.edit')
            ->with('main',$main);
    }


    public function update(StoreMainRequest $request, $id)
    {
        $main = TransportMain::find($id);
        if ($main->transport_end_place != $request->input('transport_end_place')) {
            $coordinate = coordinate_bd_decrypt($request->input('transport_end_place_longitude'),$request->input('transport_end_place_latitude'));
        }
        $main->update([
            'transport_start_place' => $request->input('transport_start_place'),
            'transport_end_place' => $request->input('transport_end_place'),
            'transport_contact_people' => $request->input('transport_contact_people'),
            'transport_contact_phone' => $request->input('transport_contact_phone'),
            'transport_start_time' => $request->input('transport_start_time'),
            'transport_goods' => [
                'transport_goods'=>$request->input('transport_goods'),
                'transport_end_place_longitude'=> $coordinate['gg_lon']??$main->transport_goods['transport_end_place_longitude'],
                'transport_end_place_latitude'=> $coordinate['gg_lat']??$main->transport_goods['transport_end_place_latitude'],
                'transport_end_place_coordsType' => 'gcj02',
            ],
            'transport_status' => $request->input('transport_status',TransportMain::TRANSPORT_STATUS_PROCESSING)
        ]);

        return redirect()->route('admin.transport.main.index')->withFlashSuccess('行程修改成功');
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
