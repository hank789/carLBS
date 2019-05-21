<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\DashboardRequest;
use App\Models\Auth\Company;
use App\Models\Transport\TransportEntity;
use App\Models\Transport\TransportMain;
use Carbon\Carbon;

/**
 * Class DashboardController.
 */
class DashboardController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index(DashboardRequest $request)
    {
        $user = $request->user();
        //车辆数
        if ($user->company_id == 1) {
            $carsCount = TransportEntity::count();
        } else {
            if ($user->company->company_type == Company::COMPANY_TYPE_MAIN) {
                $carsCount = TransportEntity::where('last_company_id',$user->company_id)->count();
            } else {
                $carsCount = TransportEntity::where('last_vendor_company_id',$user->company_id)->count();
            }
        }
        //行程总数
        if ($user->company_id == 1) {
            $mainCount = TransportMain::count();
            //行程已完成数
            $mainFinishedCount = TransportMain::where('transport_status',TransportMain::TRANSPORT_STATUS_FINISH)->count();
        } else {
            if ($user->company->company_type == Company::COMPANY_TYPE_MAIN) {
                $mainCount = TransportMain::where('company_id',$user->company_id)->count();
                //行程已完成数
                $mainFinishedCount = TransportMain::where('company_id',$user->company_id)->where('transport_status',TransportMain::TRANSPORT_STATUS_FINISH)->count();
            } else {
                $mainCount = TransportMain::where('vendor_company_id',$user->company_id)->count();
                $mainFinishedCount = TransportMain::where('vendor_company_id',$user->company_id)->where('transport_status',TransportMain::TRANSPORT_STATUS_FINISH)->count();
            }
        }

        //行程未完成数
        $mainUnFinishedCount = $mainCount - $mainFinishedCount;

        $transportChart = $this->drawTransportChart($user);

        return view('backend.dashboard')->with(compact('carsCount','mainCount','mainFinishedCount','mainUnFinishedCount','transportChart'));
    }

    private function drawTransportChart($user)
    {

        /*生成Labels*/
        $labelTimes = $chartLabels = [];

        for( $i=0 ; $i < 30 ; $i++ ){
            $labelTimes[$i] = Carbon::createFromTimestamp( Carbon::today()->timestamp - (29-$i) * 24 * 3600 );
            $chartLabels[$i] = '"'.$labelTimes[$i]->month.'月-'.$labelTimes[$i]->day.'日'.'"';
        }

        $nowTime = Carbon::now();
        if ($user->company_id == 1) {
            $mainList = TransportMain::where('transport_start_time','>',$labelTimes[0])->where('transport_start_time','<',$nowTime)->get();
        } else {
            if ($user->company->company_type == Company::COMPANY_TYPE_MAIN) {
                $mainList = TransportMain::where('company_id',$user->company_id)->where('transport_start_time','>',$labelTimes[0])->where('transport_start_time','<',$nowTime)->get();
            } else {
                $mainList = TransportMain::where('vendor_company_id',$user->company_id)->where('transport_start_time','>',$labelTimes[0])->where('transport_start_time','<',$nowTime)->get();
            }
        }

        $totalRange = $unFinishedRange = $finishedRange = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];

        for( $i=0 ; $i < 30 ; $i++ ){
            $startTime = $labelTimes[$i];
            $endTime = $nowTime;
            if(isset($labelTimes[$i+1])){
                $endTime = $labelTimes[$i+1];
            }

            foreach($mainList as $main){
                if( $main->transport_start_time >= $startTime && $main->transport_start_time < $endTime ){
                    $totalRange[$i]++;
                    if($main->transport_status == TransportMain::TRANSPORT_STATUS_FINISH ){
                        $finishedRange[$i]++;
                    } else {
                        $unFinishedRange[$i]++;
                    }
                }
            }

        }
        return ['labels'=>$chartLabels,'totalRange'=>$totalRange,'unFinishedRange'=>$unFinishedRange, 'finishedRange'=>$finishedRange];
    }
}
