<?php

namespace App\Models\Auth;

use App\Models\Auth\Traits\Method\RoleMethod;
use App\Models\Auth\Traits\Attribute\RoleAttribute;

/**
 * Class Role.
 *
 * @property-read string $action_buttons
 * @property-read string $delete_button
 * @property-read string $edit_button
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Auth\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Permission\Models\Role permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\Role query()
 * @mixin \Eloquent
 */
class Role extends \Spatie\Permission\Models\Role
{
    use RoleAttribute,
        RoleMethod;
}
