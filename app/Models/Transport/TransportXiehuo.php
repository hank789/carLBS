<?php namespace App\Models\Transport;

use App\Models\Relations\BelongsToApiUserTrait;
use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Transport\TransportXiehuo
 *
 * @author : wanghui
 * @date : 2019/1/23 6:27 PM
 * @email :    hank.HuiWang@gmail.com
 * @property-read \App\Models\Auth\ApiUser $apiUser
 * @property-read \App\Models\Transport\TransportMain $transportMain
 * @property-read \App\Models\Transport\TransportSub $transportSub
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportXiehuo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportXiehuo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportXiehuo query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $api_user_id 司机id
 * @property int $transport_main_id
 * @property int $transport_sub_id
 * @property int $xiehuo_type 卸货类型:1目的地卸货，2中途卸货
 * @property string $geohash
 * @property string|null $car_number 车牌号
 * @property array|null $transport_goods 货物信息
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportXiehuo whereAddressProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportXiehuo whereApiUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportXiehuo whereCarNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportXiehuo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportXiehuo whereGeohash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportXiehuo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportXiehuo whereTransportGoods($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportXiehuo whereTransportMainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportXiehuo whereTransportSubId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportXiehuo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportXiehuo whereXiehuoType($value)
 */
class TransportXiehuo extends Model {
    use BelongsToApiUserTrait;

    protected $table = 'transport_xiehuo';
    protected $fillable = ['api_user_id', 'transport_main_id','transport_sub_id','xiehuo_type',
        'geohash','car_number','transport_goods'];

    protected $casts = [
        'transport_goods' => 'json'
    ];

    const XIEHUO_TYPE_END = 1;
    const XIEHUO_TYPE_MIDWAY = 2;


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

    /**
     * @return string
     */
    public function getFormatXiehuoType()
    {
        switch ($this->transport_status) {
            case self::XIEHUO_TYPE_END:
                return "目的地卸货";
                break;
            case self::XIEHUO_TYPE_MIDWAY:
                return "中途卸货";
                break;
            default:
                return '';
                break;
        }
    }
}