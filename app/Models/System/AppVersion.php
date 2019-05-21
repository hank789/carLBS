<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Session
 * package App.
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id
 * @property string $app_version
 * @property string|null $package_url
 * @property int $is_ios_force 是否强更:0非强更,1强更
 * @property int $is_android_force 是否android强更:0非强更,1强更
 * @property string|null $update_msg 更新内容
 * @property int $status 状态:0未生效,1已生效
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppVersion whereAppVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppVersion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppVersion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppVersion whereIsAndroidForce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppVersion whereIsIosForce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppVersion wherePackageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppVersion whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppVersion whereUpdateMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppVersion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AppVersion whereUserId($value)
 */
class AppVersion extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_version';

    protected $fillable = ['id','user_id', 'app_version','app_name','package_url','is_ios_force','is_android_force','update_msg','status'];

    public static $appNames = [
        1 => [
            'name'=>'长江智链',
            'ios_url' => 'itms-apps://itunes.apple.com/cn/app/长江智链/id1457673059?l=zh&ls=1&mt=8',
            'android_url' => 'https://www.pgyer.com/c8n3',
            'key' => 1
        ],
        2 => [
            'name'=>'车百讯',
            'ios_url' => 'itms-apps://itunes.apple.com/cn/app/长江智链/id1457673059?l=zh&ls=1&mt=8',
            'android_url' => 'https://www.pgyer.com/Nwmq',
            'key' => 2
        ]
    ];

    /**
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 0:
                return "<span class='badge badge-secondary'>未发布</span>";
                break;
            case 1:
                return "<span class='badge badge-success'>已审核</span>";
                break;
            case -1:
                return "<span class='badge badge-warning'>已禁止</span>";
                break;
            default:
                break;
        }
    }

    public function getAppName() {
        foreach (self::$appNames as $name) {
            if ($this->app_name == $name['key']) {
                return $name['name'];
            }
        }
        return '';
    }
}
