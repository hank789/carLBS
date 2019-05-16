<?php namespace App\Http\Controllers\Backend\Map;
/**
 * @author: wanghui
 * @date: 2019/3/5 12:06 AM
 * @email:    hank.HuiWang@gmail.com
 */

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Map\ManageYingyanRequest;
use App\Models\Auth\Company;
use App\Models\Transport\TransportEntity;
use App\Models\Transport\TransportSub;
use App\Services\BaiduMap;
use App\Services\BaiduTrace;
use App\Services\GeoHash;

class YingyanController extends Controller
{
    public function searchEntity(ManageYingyanRequest $request) {
        $user = $request->user();
        $filter =  $request->input('filter'); //inactive_time:1551715294
        $query = $request->input('query'); //苏E885
        $page_index = $request->input('page_index',1); //1
        $page_size = $request->input('page_size',10); //10
        $timeStamp = $request->input('timeStamp'); //1551715893257
        $ak = $request->input('ak');
        $service_id = $request->input('service_id');
        $callback = $request->input('callback');
        \Log::info('test',$request->all());
        $queryModel = TransportEntity::where('entity_status',1);

        if ($user->company->company_type == Company::COMPANY_TYPE_MAIN) {
            $queryModel = $queryModel->where('last_company_id',$user->company_id);
        } else {
            $queryModel = $queryModel->where('last_vendor_company_id',$user->company_id);
        }

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
            $distanceDesc = '';
            if (isset($entity->entity_info['lastPosition']) && isset($entity->entity_info['lastSub']['transport_end_place_longitude'])) {
                $end_place = [];
                $end_place['bd_lon'] = $entity->entity_info['lastSub']['transport_end_place_longitude'];
                $end_place['bd_lat'] = $entity->entity_info['lastSub']['transport_end_place_latitude'];
                if ($entity->entity_info['lastSub']['transport_end_place_coordsType'] != '﻿bd09ll') {
                    $end_place = coordinate_bd_encrypt($entity->entity_info['lastSub']['transport_end_place_longitude'],$entity->entity_info['lastSub']['transport_end_place_latitude']);
                }
                $distance = getDistanceByLatLng($entity->entity_info['lastPosition']['longitude'],$entity->entity_info['lastPosition']['latitude'],$end_place['bd_lon'],$end_place['bd_lat']);
                $distanceDesc = '距目的地约'.distanceFormat($distance);
            }
            $latest_location = $entity->entity_info['lastPosition']??[];
            if (isset($latest_location['speed'])) {
                $latest_location['speed'] = round($latest_location['speed'],2);
            }
            $list[] = [
                'entity_name' => $entity->car_number,
                'entity_owner' => ($entity->entity_info['lastSub']['username']??'').' '.($entity->entity_info['lastSub']['phone']??''),
                'distance' => $distanceDesc,
                'end_place' => ($entity->entity_info['lastSub']['transport_end_place']??''),
                'entity_desc' => ($entity->entity_info['lastSub']['goods_info']??''),
                'create_time' => $entity->entity_info['lastSub']['start_time']??(string)$entity->created_at,
                'modify_time' => (string)$entity->last_loc_time,
                'view_url' => route('admin.transport.sub.show', $entity->entity_info['lastSub']['sub_id']),
                'latest_location' => $latest_location
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
        $user = $request->user();
        $params = $request->all();
        $useBaidu = false;
        if ($useBaidu) {
            unset($params['callback']);
            $res = BaiduTrace::instance()->boundsearchEntity($params);
            return response()->json($res);
        } else {
            //计算两点距离
            $bounds = explode(';',$params['bounds']);
            $LatLan1 = explode(',',$bounds[0]);
            $LatLan2 = explode(',',$bounds[1]);
            $distance = getDistanceByLatLng($LatLan1[1],$LatLan1[0],$LatLan2[1],$LatLan2[0]);
            $halfDistance =  $distance/2;
            //中点坐标
            $midLan = ($LatLan1[1] + $LatLan2[1])/2;
            $midLat = ($LatLan1[0] + $LatLan2[0])/2;

            $hash = GeoHash::instance()->encode($midLat,$midLan);

            if ($halfDistance <= 5000) {
                //5公里以内
                $hashLength = 5;
            } elseif ($halfDistance <= 20000) {
                //20公里以内
                $hashLength = 4;
            } elseif ($halfDistance <= 156000) {
                //156公里以内
                $hashLength = 3;
            } elseif ($halfDistance <= 625000) {
                //625公里以内
                $hashLength = 2;
            } else {
                $hashLength = 1;
            }

            $pre_hash = substr($hash, 0, $hashLength);

            //取出相邻八个区域
            $neighbors = GeoHash::instance()->neighbors($pre_hash);
            array_push($neighbors, $pre_hash);

            $values = '';
            foreach ($neighbors as $key=>$val) {
                $values .= '\'' . $val . '\'' .',';
            }
            $values = substr($values, 0, -1);

            $filter =  $request->input('filter'); //inactive_time:1551715294
            $query = $request->input('query'); //苏E885
            $page_index = $request->input('page_index',1); //1
            $page_size = $request->input('page_size',10); //10
            $timeStamp = $request->input('timeStamp'); //1551715893257
            \Log::info('test',$request->all());
            $queryModel = TransportEntity::where('entity_status',1);

            if ($user->company->company_type == Company::COMPANY_TYPE_MAIN) {
                $queryModel = $queryModel->where('last_company_id',$user->company_id);
            } else {
                $queryModel = $queryModel->where('last_vendor_company_id',$user->company_id);
            }

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
            $queryModel = $queryModel->whereRaw('LEFT(`last_geohash`,'.$hashLength.') IN ('.$values.')');
            $entities = $queryModel->orderBy('last_loc_time','desc')->paginate($page_size,['*'],'page',$page_index);
            $list = [];
            $return = [];
            foreach ($entities as $entity) {
                $distanceDesc = '';
                if (isset($entity->entity_info['lastPosition']) && isset($entity->entity_info['lastSub']['transport_end_place_longitude'])) {
                    $end_place = [];
                    $end_place['bd_lon'] = $entity->entity_info['lastSub']['transport_end_place_longitude'];
                    $end_place['bd_lat'] = $entity->entity_info['lastSub']['transport_end_place_latitude'];
                    if ($entity->entity_info['lastSub']['transport_end_place_coordsType'] != '﻿bd09ll') {
                        $end_place = coordinate_bd_encrypt($entity->entity_info['lastSub']['transport_end_place_longitude'],$entity->entity_info['lastSub']['transport_end_place_latitude']);
                    }
                    $distance = getDistanceByLatLng($entity->entity_info['lastPosition']['longitude'],$entity->entity_info['lastPosition']['latitude'],$end_place['bd_lon'],$end_place['bd_lat']);
                    $distanceDesc = '距目的地约'.distanceFormat($distance);
                }
                $latest_location = $entity->entity_info['lastPosition']??[];
                if (isset($latest_location['speed'])) {
                    $latest_location['speed'] = round($latest_location['speed'],2);
                }
                $list[] = [
                    'entity_name' => $entity->car_number,
                    'entity_owner' => ($entity->entity_info['lastSub']['username']??'').' '.($entity->entity_info['lastSub']['phone']??''),
                    'distance' => $distanceDesc,
                    'end_place' => ($entity->entity_info['lastSub']['transport_end_place']??''),
                    'entity_desc' => ($entity->entity_info['lastSub']['goods_info']??''),
                    'create_time' => $entity->entity_info['lastSub']['start_time']??(string)$entity->created_at,
                    'modify_time' => (string)$entity->last_loc_time,
                    'view_url' => route('admin.transport.sub.show', $entity->entity_info['lastSub']['sub_id']),
                    'latest_location' => $latest_location
                ];
            }
            $return['entities'] = $list;
            $return['status'] = 0;
            $return['message'] = '成功';
            $return['size'] = count($list);
            $return['total'] = $entities->total();
            return response()->json($return);
        }

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