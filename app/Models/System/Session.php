<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Session
 * package App.
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\System\Session newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\System\Session newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\System\Session query()
 * @mixin \Eloquent
 */
class Session extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sessions';

    /**
     * @var array
     */
    protected $guarded = ['*'];
}
