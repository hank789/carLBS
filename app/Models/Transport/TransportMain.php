<?php namespace App\Models\Transport;

use App\Models\Relations\BelongsToSystemUserTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
class TransportMain extends Model {
    use BelongsToSystemUserTrait, SoftDeletes;

    protected $table = 'transport_main';
    protected $fillable = ['user_id', 'transport_number','transport_start_place','transport_end_place',
        'transport_contact_people','transport_contact_phone','transport_contact_vendor_people','transport_contact_vendor_phone','transport_start_time','transport_goods','transport_status'];

    const TRANSPORT_STATUS_CANCEL = -1;
    const TRANSPORT_STATUS_PENDING = 0;
    const TRANSPORT_STATUS_PROCESSING = 1;
    const TRANSPORT_STATUS_FINISH = 2;
    const TRANSPORT_STATUS_OVERTIME_FINISH = 3;


    protected $casts = [
        'transport_goods' => 'json'
    ];

    /**
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        switch ($this->transport_status) {
            case self::TRANSPORT_STATUS_PENDING:
                return "<span class='badge badge-secondary'>未发布</span>";
                break;
            case self::TRANSPORT_STATUS_PROCESSING:
                $subCount = $this->getTransportSubCount();
                if ($subCount > 0) {
                    return "<span class='badge badge-info'>运输中</span>";
                }
                return "<span class='badge badge-info'>司机尚未接单</span>";
                break;
            case self::TRANSPORT_STATUS_FINISH:
                return "<span class='badge badge-success'>已完成</span>";
                break;
            case self::TRANSPORT_STATUS_CANCEL:
                return "<span class='badge badge-warning'>已取消</span>";
                break;
            case self::TRANSPORT_STATUS_OVERTIME_FINISH:
                return "<span class='badge badge-warning'>超时自动结束</span>";
            default:
                break;
        }
    }

    /**
     * @return string
     */
    public function getStatusButtonAttribute()
    {
        switch ($this->transport_status) {
            case self::TRANSPORT_STATUS_PENDING:
                return '<a data-source_id="'.$this->id.'" data-source_msg="确认发布此行程？" data-source_status="'.self::TRANSPORT_STATUS_PROCESSING.'" class="dropdown-item btn-confirm">发布行程</a> '.'<a data-source_id="'.$this->id.'" data-source_msg="确认取消此行程？" data-source_status="'.self::TRANSPORT_STATUS_CANCEL.'" class="dropdown-item btn-confirm">取消行程</a> ';

            case self::TRANSPORT_STATUS_PROCESSING:
                return '<a data-source_id="'.$this->id.'" data-source_msg="确认结束此行程？" data-source_status="'.self::TRANSPORT_STATUS_FINISH.'" class="dropdown-item btn-confirm">结束行程</a> '.'<a data-source_id="'.$this->id.'" data-source_msg="确认将此行程调整为待发布？" data-source_status="'.self::TRANSPORT_STATUS_PENDING.'" class="dropdown-item btn-confirm">调整为待发布</a> ';

            default:
                return '';
        }
    }

    /**
     * @return string
     */
    public function getShowButtonAttribute()
    {
        return '<a href="'.route('admin.transport.main.show', $this->id).'" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.view').'" class="btn btn-info"><i class="fas fa-eye"></i></a>';
    }

    /**
     * @return string
     */
    public function getEditButtonAttribute()
    {
        return '<a href="'.route('admin.transport.main.edit', $this->id).'" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.edit').'" class="btn btn-primary"><i class="fas fa-edit"></i></a>';
    }

    /**
     * @return string
     */
    public function getActionButtonsAttribute()
    {
        return '
    	<div class="btn-group" role="group" aria-label="'.__('labels.backend.access.users.user_actions').'">
		  '.$this->show_button.'
          '.$this->edit_button.'
		  <div class="btn-group btn-group-sm" role="group">
			<button id="userActions" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			  操作
			</button>
			<div class="dropdown-menu" aria-labelledby="userActions">
			  '.$this->status_button.'
			</div>
		  </div>
		</div>';
    }


    public function getTransportEventCount() {
        return TransportEvent::where('transport_main_id',$this->id)->count();
    }

    public function getTransportXiehuoCount() {
        return TransportXiehuo::where('transport_main_id',$this->id)->count();
    }

    public function getTransportSubCount() {
        return TransportSub::where('transport_main_id',$this->id)->count();
    }
}