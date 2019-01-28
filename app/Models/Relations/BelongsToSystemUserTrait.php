<?php namespace App\Models\Relations;
/**
 * @author: wanghui
 * @date: 2019/1/23 6:29 PM
 * @email:    hank.HuiWang@gmail.com
 */

trait BelongsToSystemUserTrait
{
    /**
     * Get the user relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function systemUser()
    {
        return $this->belongsTo('App\Models\Auth\User','user_id','id');
    }
}