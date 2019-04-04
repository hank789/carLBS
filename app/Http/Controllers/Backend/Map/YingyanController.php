<?php namespace App\Http\Controllers\Backend\Map;
/**
 * @author: wanghui
 * @date: 2019/3/5 12:06 AM
 * @email:    hank.HuiWang@gmail.com
 */

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Map\ManageYingyanRequest;
use App\Models\Transport\TransportEntity;
use App\Models\Transport\TransportSub;
use App\Services\BaiduMap;
use App\Services\BaiduTrace;

class YingyanController extends Controller
{
    public function searchEntity(ManageYingyanRequest $request) {
        $filter =  $request->input('filter'); //inactive_time:1551715294
        $query = $request->input('query'); //苏E885
        $page_index = $request->input('page_index',1); //1
        $page_size = $request->input('page_size',10); //10
        $timeStamp = $request->input('timeStamp'); //1551715893257
        $ak = $request->input('ak');
        $service_id = $request->input('service_id');
        $callback = $request->input('callback');
        \Log::info('test',$request->all());
        $queryModel = TransportEntity::query();
        if ($query) {
            $queryModel = $queryModel->where('car_number','like','%'.$query.'%');
        }
        if ($filter) {
            //目前只支持active_time和inactive_time的检索
            $exFilter = explode('|',$filter);
            foreach ($exFilter as $i) {
                $item = explode(':',$i);
                switch ($item[0]) {
                    case 'inactive_time':
                        $queryModel = $queryModel->where('last_loc_time','<',date('Y-m-d H:i:s',$item[1]));
                        break;
                    case 'active_time':
                        $queryModel = $queryModel->where('last_loc_time','>=',date('Y-m-d H:i:s',$item[1]));
                        break;
                }
            }
        }
        $entities = $queryModel->orderBy('last_loc_time','desc')->paginate($page_size,['*'],'page',$page_index);
        $list = [];
        $return = [];
        foreach ($entities as $entity) {
            $list[] = [
                'entity_name' => $entity->car_number,
                'entity_desc' => '司机：'.($entity->entity_info['lastSub']['username']??'').' '.($entity->entity_info['lastSub']['phone']??'').'<br>货物：'.($entity->entity_info['lastSub']['goods_info']),
                'create_time' => $entity->entity_info['lastSub']['start_time']??(string)$entity->created_at,
                'modify_time' => (string)$entity->last_loc_time,
                'latest_location' => $entity->entity_info['lastPosition']??[]
            ];
        }
        $return['entities'] = $list;
        $return['status'] = 0;
        $return['message'] = '成功';
        $return['size'] = count($list);
        $return['total'] = $entities->total();
        if ($callback) {
            return response()->jsonp($callback,$return);
        } else {
            return response()->json($return);
        }
    }

    // 矩形区域检索entity:yingyan.baidu.com/api/v3/entity/boundsearch
    public function boundsearchEntity(ManageYingyanRequest $request) {
        $params = $request->all();
        unset($params['callback']);
        $res = BaiduTrace::instance()->boundsearchEntity($params);
        return response()->json($res);
    }

    //获取track的distance:yingyan.baidu.com/api/v3/track/getdistance
    public function getDistance(ManageYingyanRequest $request) {
        $params = $request->all();
        unset($params['callback']);
        $res = BaiduTrace::instance()->getDistance($params);
        return response()->json($res);
    }

    //获取track信息:yingyan.baidu.com/api/v3/track/gettrack
    public function getTrack(ManageYingyanRequest $request) {
        $params = $request->all();
        unset($params['callback']);
        $res = BaiduTrace::instance()->getTrack($params);
        return response()->json($res);
    }

    //获取自定义字段列表:yingyan.baidu.com/api/v3/entity/listcolumn
    public function columnsList(ManageYingyanRequest $request) {
        return response()->json([
            'status' => 0,
            'message' => '成功',
            'columns' => []
        ]);
        $params = $request->all();
        unset($params['callback']);
        $res = BaiduTrace::instance()->columnsList($params);
        return response()->json($res);
    }

    //获取track列表:yingyan.baidu.com/api/v2/track/gethistory
    public function trackList(ManageYingyanRequest $request) {
        $params = $request->all();
        unset($params['callback']);
        $res = BaiduTrace::instance()->setVersion('v2')->trackList($params);
        return response()->json($res);
    }

    //获取停留点:yingyan.baidu.com/api/v3/analysis/staypoint
    public function getstaypoint(ManageYingyanRequest $request) {
        $params = $request->all();
        unset($params['callback']);
        $res = BaiduTrace::instance()->getstaypoint($params);
        return response()->json($res);
    }

    //获取驾驶行为分析信息:yingyan.baidu.com/api/v3/analysis/drivingbehavior
    public function getBehaviorAnalysis(ManageYingyanRequest $request) {
        $params = $request->all();
        unset($params['callback']);
        $res = BaiduTrace::instance()->getBehaviorAnalysis($params);
        return response()->json($res);
    }

    //经纬度解析:api.map.baidu.com/geocoder/v2/
    public function getAddress(ManageYingyanRequest $request) {
        $params = $request->all();
        unset($params['callback']);
        $location = explode(',',$params['location']);
        $res = BaiduMap::instance()->geocoder($location[0],$location[1]);
        return response()->json($res);
    }

}