<?php namespace App\Models\System;
/**
 * @author: wanghui
 * @date: 2019/1/23 6:46 PM
 * @email:    hank.HuiWang@gmail.com
 */

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Setting
 *
 * @property string $name
 * @property string $value
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\System\Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\System\Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\System\Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\System\Setting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\System\Setting whereValue($value)
 */
class Setting extends Model
{

    protected $table = 'settings';
    protected $primaryKey = 'name';
    protected $fillable = ['name', 'value'];
    public $timestamps = false;


    public static function loadAll()
    {
        $settings = self::all();
        //  print_r($settings);
    }


    /*查询某个配置信息*/
    public static function get($name,$default='')
    {
        $setting =  self::where('name','=',$name)->first();
        if($setting){
            return $setting->value;
        }
        return $default;
    }


    public static function set($name , $value)
    {
        self::updateOrCreate(['name'=>$name],['value'=>$value]);
    }


    public static function writeToEnv(){
        $env_path = base_path('.env');
        $env_content = '';
        ksort($_ENV);
        foreach($_ENV as $key => $value ){
            $env_content .= $key.'='.$value."\n";
        }
        file_put_contents($env_path,$env_content);
    }



    /*清空配置缓存*/
    public static function clearAll()
    {

    }

}