<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
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
    public function index()
    {
        //车辆数
        $carsCount = TransportEntity::count();
        //行程总数
        $mainCount = TransportMain::count();
        //行程已完成数
        $mainFinishedCount = TransportMain::where('transport_status',TransportMain::TRANSPORT_STATUS_FINISH)->count();
        //行程未完成数
        $mainUnFinishedCount = $mainCount - $mainFinishedCount;

        $transportChart = $this->drawTransportChart();

        return view('backend.dashboard')->with(compact('carsCount','mainCount','mainFinishedCount','mainUnFinishedCount','transportChart'));
    }

    private function drawTransportChart()
    {

        /*生成Labels*/
        $labelTimes = $chartLabels = [];

        for( $i=0 ; $i < 30 ; $i++ ){
            $labelTimes[$i] = Carbon::createFromTimestamp( Carbon::today()->timestamp - (29-$i) * 24 * 3600 );
            $chartLabels[$i] = '"'.$labelTimes[$i]->month.'月-'.$labelTimes[$i]->day.'日'.'"';
        }

        $nowTime = Carbon::now();

        $mainList = TransportMain::where('transport_start_time','>',$labelTimes[0])->where('transport_start_time','<',$nowTime)->get();

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
