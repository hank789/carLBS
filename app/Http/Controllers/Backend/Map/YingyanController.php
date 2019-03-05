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
                'entity_desc' => $entity->entity_info['username']??'',
                'create_time' => $entity->entity_info['start_time']??(string)$entity->created_at,
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
}