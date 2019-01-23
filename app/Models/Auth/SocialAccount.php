<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SocialAccount.
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\SocialAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\SocialAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Auth\SocialAccount query()
 * @mixin \Eloquent
 */
class SocialAccount extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'social_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'provider', 'provider_id', 'token', 'avatar'];
}
