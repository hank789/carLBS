<?php namespace App\Models\Transport;

use App\Models\Relations\BelongsToApiUserTrait;
use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Transport\TransportLbs
 *
 * @author : wanghui
 * @date : 2019/1/23 6:27 PM
 * @email :    hank.HuiWang@gmail.com
 * @property-read \App\Models\Auth\ApiUser $apiUser
 * @property-read \App\Models\Transport\TransportMain $transportMain
 * @property-read \App\Models\Transport\TransportSub $transportSub
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportLbs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportLbs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportLbs query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $api_user_id 司机id
 * @property int $transport_main_id
 * @property int $transport_sub_id
 * @property string $address_province 省市地址
 * @property string $address_detail 详细地址
 * @property string $longitude 经度
 * @property string $latitude 纬度
 * @property string $geohash
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportLbs whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportLbs whereAddressProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportLbs whereApiUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportLbs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportLbs whereGeohash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportLbs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportLbs whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportLbs whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportLbs whereTransportMainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportLbs whereTransportSubId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportLbs whereUpdatedAt($value)
 */
class TransportLbs extends Model {
    use BelongsToApiUserTrait;

    protected $table = 'transport_lbs';
    protected $fillable = ['api_user_id', 'transport_main_id','transport_sub_id','address_detail','created_at', 'updated_at'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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