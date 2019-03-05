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

    const TRANSPORT_STATUS_CANCLE = -1;
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
        $transportGoods['lastPosition'] = BaiduTrace::instance()->formatGeoLocation($lastPosition,true,true);
        $this->transport_goods = $transportGoods;
        $this->last_loc_time = date('Y-m-d H:i:s',$lastPosition['timestamp']);
        $this->save();
        $entity = $this->transportEntity;
        $entity->last_loc_time = date('Y-m-d H:i:s',$lastPosition['timestamp']);
        $entity_info = $entity->entity_info;
        $entity_info['lastPosition'] = $transportGoods['lastPosition'];
        $entity->entity_info = $entity_info;
        $entity->save();
    }

}