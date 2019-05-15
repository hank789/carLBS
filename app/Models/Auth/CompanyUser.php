<?php namespace App\Models\Auth;
/**
 * @author: wanghui
 * @date: 2019/1/23 6:27 PM
 * @email:    hank.HuiWang@gmail.com
 */


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserDevice
 *
 * @author : wanghui
 * @date : 2017/5/9 下午6:00
 * @email : hank.huiwang@gmail.com
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id
 * @property string $client_id 推送设备唯一标示
 * @property string $device_token Android - 2.2+ (支持): 设备的唯一标识号，通常与clientid值一致。iOS - 4.5+ (支持): 设备的DeviceToken值，向APNS服务器发送推送消息时使用
 * @property string|null $appid 第三方推送服务的应用标识
 * @property string|null $appkey 第三方推送服务器的应用键值
 * @property int $device_type 设备类型,1安卓,2苹果
 * @property int $status 状态:1登陆,0未登录
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Auth\ApiUser $apiUser
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\UserDevice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\UserDevice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\UserDevice query()
 * @property int $api_user_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\UserDevice whereApiUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\UserDevice whereAppid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\UserDevice whereAppkey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\UserDevice whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\UserDevice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\UserDevice whereDeviceToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\UserDevice whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\UserDevice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\UserDevice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\UserDevice whereUpdatedAt($value)
 */
class CompanyUser extends Model {

    protected $table = 'company_user_rel';
    protected $fillable = ['company_id','user_id','company_type','status'];

    const COMPANY_TYPE_MAIN = 1;
    const COMPANY_TYPE_VENDOR = 2;
}