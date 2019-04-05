<?php

namespace App\Http\Controllers\Backend\Transport;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Transport\ManageMainRequest;
use App\Http\Requests\Backend\Transport\StoreMainRequest;
use App\Models\Auth\ApiUser;
use App\Models\Transport\TransportEvent;
use App\Models\Transport\TransportMain;
use App\Models\Transport\TransportSub;
use App\Models\Transport\TransportXiehuo;
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
        $sub = TransportSub::find($id);
        $main = $sub->transportMain;
        $events = TransportEvent::where('transport_sub_id',$id)->orderBy('id','desc')->get();
        $xiehuos = TransportXiehuo::where('transport_sub_id',$id)->orderBy('id','desc')->get();
        $timeline = [];
        $eventType = [];
        foreach (TransportEvent::$eventType as $e) {
            $eventType[$e['key']] = $e['value'];
        }
        $timeline[(string)$sub->created_at] = [
            'title' => '行程开始',
            'place' => $sub->transport_start_place,
            'desc'  => '',
            'images' => $sub->transport_goods['transport_goods_images']??[],
            'created_at' => $sub->transport_goods['transport_start_real_time'],
            'icon' => 'fa-clock',
            'bg_color' => 'lazur-bg'
        ];
        foreach ($events as $event) {
            $timeline[(string)$event->created_at] = [
                'title' => '突发事件',
                'place' => $event->event_detail['event_place'],
                'desc'  => ''.$eventType[$event->event_type].','.$event->event_detail['description'],
                'images' => $event->event_detail['images'],
                'created_at' => $event->created_at,
                'icon' => 'fa-exclamation-circle',
                'bg_color' => 'yellow-bg'
            ];
        }
        foreach ($xiehuos as $xiehuo) {
            $timeline[(string)$xiehuo->created_at] = [
                'title' => $xiehuo->xiehuo_type == TransportXiehuo::XIEHUO_TYPE_MIDWAY ? '中途卸货':'目的地卸货',
                'place' => $xiehuo->transport_goods['transport_end_place'],
                'desc'  => '车牌号：'.$xiehuo->transport_goods['car_number'].';货物：'.$xiehuo->transport_goods['transport_goods'],
                'images' => $xiehuo->transport_goods['shipping_documents'],
                'created_at' => $xiehuo->created_at,
                'icon' => $xiehuo->xiehuo_type == TransportXiehuo::XIEHUO_TYPE_MIDWAY ? 'fa-truck':'fa-flag-checkered',
                'bg_color' => $xiehuo->xiehuo_type == TransportXiehuo::XIEHUO_TYPE_MIDWAY ? 'navy-bg':'blue-bg'
            ];
        }
        if ($timeline) {
            krsort($timeline);
        }

        return view('backend.transport.sub.show')
            ->with('main',$main)->with('sub',$sub)->with('timeline',$timeline);
    }
}
