<?php namespace App\Models\Transport;

use App\Models\Relations\BelongsToApiUserTrait;
use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Transport\TransportEvent
 *
 * @author : wanghui
 * @date : 2019/1/23 6:27 PM
 * @email :    hank.HuiWang@gmail.com
 * @property-read \App\Models\Auth\ApiUser $apiUser
 * @property-read \App\Models\Transport\TransportMain $transportMain
 * @property-read \App\Models\Transport\TransportSub $transportSub
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportEvent query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $api_user_id 司机id
 * @property int $transport_main_id
 * @property int $transport_sub_id
 * @property int $event_type 事件类型
 * @property string $geohash
 * @property array|null $event_detail 事件描述
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportEvent whereAddressProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportEvent whereApiUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportEvent whereEventDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportEvent whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportEvent whereGeohash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportEvent whereTransportMainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportEvent whereTransportSubId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportEvent whereUpdatedAt($value)
 */
class TransportEvent extends Model {
    use BelongsToApiUserTrait;

    protected $table = 'transport_events';
    protected $fillable = ['api_user_id', 'transport_main_id','transport_sub_id','event_type',
        'geohash','event_detail'];

    protected $casts = [
        'event_detail' => 'json'
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

    public function transportSub() {
        return $this->belongsTo('App\Models\Transport\TransportSub');
    }
}