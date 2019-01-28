<?php namespace App\Models\Transport;
/**
 * App\Models\Transport\TransportMain
 *
 * @author : wanghui
 * @date : 2019/1/23 6:27 PM
 * @email :    hank.HuiWang@gmail.com
 * @property-read \App\Models\Auth\User $systemUser
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportMain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportMain newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transport\TransportMain onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportMain query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transport\TransportMain withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transport\TransportMain withoutTrashed()
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id 本次行程的录入人员
 * @property string $transport_number 行程编码
 * @property int $transport_status 状态
 * @property string|null $transport_start_place 行程出发地
 * @property string|null $transport_end_place 行程目的地
 * @property string|null $transport_contact_people 本次行程的联系人
 * @property string|null $transport_contact_phone 本次行程的联系电话
 * @property string|null $transport_start_time 行程出发时间
 * @property string|null $transport_goods 货物信息
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportMain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportMain whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportMain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportMain whereTransportContactPeople($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportMain whereTransportContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportMain whereTransportEndPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportMain whereTransportGoods($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportMain whereTransportNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportMain whereTransportStartPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportMain whereTransportStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportMain whereTransportStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportMain whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transport\TransportMain whereUserId($value)
 */


use App\Models\Relations\BelongsToSystemUserTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TransportMain extends Model {
    use BelongsToSystemUserTrait, SoftDeletes;

    protected $table = 'transport_main';
    protected $fillable = ['user_id', 'transport_number','transport_start_place','transport_end_place',
        'transport_contact_people','transport_contact_phone','transport_start_time','transport_goods','transport_status'];

    const TRANSPORT_STATUS_CANCEL = -1;
    const TRANSPORT_STATUS_PENDING = 0;
    const TRANSPORT_STATUS_PROCESSING = 1;
    const TRANSPORT_STATUS_FINISH = 2;

}