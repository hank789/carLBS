<?php namespace App\Models\Transport;

use App\Models\Relations\BelongsToApiUserTrait;
use App\Services\BaiduTrace;
use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Transport\TransportSub
 *
 * @author : wanghui
 * @date : 2019/1/23 6:27 PM
 * @email :    hank.HuiWang@gmail.com
 * @property-read \App\Models\Auth\ApiUser $apiUser
 * @property-read \App\Models\Transport\TransportMain $transportMain
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportSub newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportSub newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportSub query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $transport_main_id
 * @property int $api_user_id 司机id
 * @property int $transport_status 状态
 * @property string|null $car_number 车牌号
 * @property string|null $transport_start_time 行程出发时间
 * @property string|null $transport_start_place 行程出发地
 * @property string|null $transport_end_place 行程目的地
 * @property string|null $transport_goods 货物信息
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportSub whereApiUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportSub whereCarNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportSub whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportSub whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportSub whereTransportEndPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportSub whereTransportGoods($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportSub whereTransportMainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportSub whereTransportStartPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportSub whereTransportStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportSub whereTransportStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportSub whereUpdatedAt($value)
 */
class TransportSub extends Model {
    use BelongsToApiUserTrait;

    protected $table = 'transport_sub';
    protected $fillable = ['api_user_id', 'transport_main_id','transport_entity_id','transport_start_place','transport_end_place',
        'transport_start_time','transport_goods','transport_status','last_loc_time'];

    const TRANSPORT_STATUS_CANCEL = -1;
    const TRANSPORT_STATUS_PENDING = 0;
    const TRANSPORT_STATUS_PROCESSING = 1;
    const TRANSPORT_STATUS_FINISH = 2;

    protected $casts = [
        'transport_goods' => 'json'
    ];

    /**
     * Get the transportMain relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transportMain()
    {
        return $this->belongsTo('App\Models\Transport\TransportMain');
    }

    /**
     * Get the transportMain relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transportEntity()
    {
        return $this->belongsTo('App\Models\Transport\TransportEntity');
    }

    public function getEntityName() {
        return $this->transportEntity->car_number;
    }

    public function saveLastPosition(array $lastPosition) {
        $transportGoods = $this->transport_goods;
        $oldLastPosition = $transportGoods['lastPosition']??'';
        $transportGoods['lastPosition'] = BaiduTrace::instance()->formatGeoLocation($lastPosition,true,true);
        $timeDiff = $transportGoods['lastPosition']['loc_time'] - $oldLastPosition['loc_time'];
        if (empty($transportGoods['lastPosition']['speed']) && $oldLastPosition && $timeDiff>0 && $timeDiff <= 60*5) {
            $distance = getDistanceByLatLng($transportGoods['lastPosition']['longitude'],$transportGoods['lastPosition']['latitude'],$oldLastPosition['longitude'],$oldLastPosition['latitude']);
            $transportGoods['lastPosition']['speed'] = $distance/$timeDiff * 3.6;
        }

        $this->transport_goods = $transportGoods;
        $this->last_loc_time = date('Y-m-d H:i:s',$transportGoods['lastPosition']['loc_time']);
        $this->save();
        $entity = $this->transportEntity;
        $entity->last_loc_time = date('Y-m-d H:i:s',$transportGoods['lastPosition']['loc_time']);
        $entity_info = $entity->entity_info;

        $entity_info['lastPosition'] = $transportGoods['lastPosition'];
        $entity->entity_info = $entity_info;
        $entity->save();
    }

    public function getTransportEventCount() {
        return TransportEvent::where('transport_sub_id',$this->id)->count();
    }

    public function getTransportXiehuoCount() {
        return TransportXiehuo::where('transport_sub_id',$this->id)->count();
    }

    /**
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        switch ($this->transport_status) {
            case self::TRANSPORT_STATUS_PENDING:
                return "<span class='badge badge-secondary'>未开始</span>";
                break;
            case self::TRANSPORT_STATUS_PROCESSING:
                return "<span class='badge badge-info'>运输中</span>";
                break;
            case self::TRANSPORT_STATUS_FINISH:
                return "<span class='badge badge-success'>已完成</span>";
                break;
            case self::TRANSPORT_STATUS_CANCEL:
                return "<span class='badge badge-warning'>已取消</span>";
                break;
            default:
                break;
        }
    }

    /**
     * @return string
     */
    public function getShowButtonAttribute()
    {
        return '<a href="'.route('admin.transport.sub.show', $this->id).'" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.view').'">查看</a>';
    }

}