<?php
/**
 * @author: wanghui
 * @date: 2019/7/30 5:12 PM
 * @email:    hank.HuiWang@gmail.com
 */

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $table = 'tenant';
    protected $fillable = ['request_id','user_id','app_type','app_id','tenant_id','source',
        'status','detail'];


    protected $casts = [
        'detail' => 'json'
    ];
}