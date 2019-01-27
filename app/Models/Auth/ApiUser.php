<?php

namespace App\Models\Auth;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
/**
 * Class User.
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\ApiUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\ApiUser newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\ApiUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\ApiUser permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\ApiUser query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\ApiUser role($roles)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\ApiUser withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\ApiUser withoutTrashed()
 * @mixin \Eloquent
 * @property-read mixed $full_name
 * @property int $id
 * @property string $name
 * @property string|null $mobile
 * @property int|null $gender
 * @property int $status
 * @property string|null $last_login_token 上次登录token
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property string|null $last_login_ip
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\ApiUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\ApiUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\ApiUser whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\ApiUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\ApiUser whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\ApiUser whereLastLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\ApiUser whereLastLoginToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\ApiUser whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\ApiUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\ApiUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\ApiUser whereUpdatedAt($value)
 */
class ApiUser extends Authenticatable implements JWTSubject
{
    use HasRoles,
        Notifiable,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'mobile',
        'gender',
        'status',
        'trip_number',
        'last_login_token',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * @var array
     */
    protected $dates = ['last_login_at', 'deleted_at'];

    protected $table = 'api_users';

    public function getFullNameAttribute()
    {
        return $this->name;
    }

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'exp' => strtotime('+10 years', time()),
            'auth_type' => 'apiUser'
        ];
    }

    public function isActive()
    {
        return $this->status == 1;
    }

    /**
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        if ($this->isActive()) {
            return "<span class='badge badge-success'>".__('labels.general.active').'</span>';
        }

        return "<span class='badge badge-danger'>".__('labels.general.inactive').'</span>';
    }

    /**
     * @return string
     */
    public function getStatusButtonAttribute()
    {
        if ($this->id != auth()->id()) {
            switch ($this->isActive()) {
                case 0:
                    return '<a href="'.route('admin.transport.user.mark', [
                            $this->id,
                            1,
                        ]).'" class="dropdown-item">'.__('buttons.backend.access.users.activate').'</a> ';

                case 1:
                    return '<a href="'.route('admin.transport.user.mark', [
                            $this->id,
                            0,
                        ]).'" class="dropdown-item">'.__('buttons.backend.access.users.deactivate').'</a> ';

                default:
                    return '';
            }
        }

        return '';
    }

    /**
     * @return string
     */
    public function getShowButtonAttribute()
    {
        return '<a href="'.route('admin.transport.user.show', $this).'" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.view').'" class="btn btn-info"><i class="fas fa-eye"></i></a>';
    }

    /**
     * @return string
     */
    public function getActionButtonsAttribute()
    {
        return '
    	<div class="btn-group" role="group" aria-label="'.__('labels.backend.access.users.user_actions').'">
		  '.$this->show_button.'

		  <div class="btn-group btn-group-sm" role="group">
			<button id="userActions" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			  '.__('labels.general.more').'
			</button>
			<div class="dropdown-menu" aria-labelledby="userActions">
			  '.$this->status_button.'
			</div>
		  </div>
		</div>';
    }
}
