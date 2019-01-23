<?php

namespace App\Models\Auth;

use App\Models\Traits\Uuid;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use App\Models\Auth\Traits\Scope\UserScope;
use App\Models\Auth\Traits\Method\UserMethod;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Auth\Traits\SendUserPasswordReset;
use App\Models\Auth\Traits\Attribute\UserAttribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Auth\Traits\Relationship\UserRelationship;

/**
 * Class User.
 *
 * @property-read string $action_buttons
 * @property-read string $change_password_button
 * @property-read string $clear_session_button
 * @property-read string $confirmed_button
 * @property-read string $confirmed_label
 * @property-read string $delete_button
 * @property-read string $delete_permanently_button
 * @property-read string $edit_button
 * @property-read string $full_name
 * @property-read string $login_as_button
 * @property-read string $name
 * @property-read string $permissions_label
 * @property-read mixed $picture
 * @property-read string $restore_button
 * @property-read string $roles_label
 * @property-read string $show_button
 * @property-read string $social_buttons
 * @property-read string $status_button
 * @property-read string $status_label
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Auth\PasswordHistory[] $passwordHistories
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Auth\SocialAccount[] $providers
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\System\Session[] $sessions
 * @property-write mixed $password
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\User active($status = true)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\User confirmed($confirmed = true)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\User newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\User query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\User role($roles)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\User uuid($uuid)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasRoles,
        Notifiable,
        SendUserPasswordReset,
        SoftDeletes,
        UserAttribute,
        UserMethod,
        UserRelationship,
        UserScope,
        Uuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'avatar_type',
        'avatar_location',
        'password',
        'password_changed_at',
        'active',
        'confirmation_code',
        'confirmed',
        'timezone',
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

    /**
     * The dynamic attributes from mutators that should be returned with the user object.
     * @var array
     */
    protected $appends = ['full_name'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'confirmed' => 'boolean',
    ];
}
