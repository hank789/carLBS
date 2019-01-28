<?php namespace App\Services;
use Illuminate\Support\Facades\Redis;

/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 15/9/29
 * Time: 下午3:50
 */

class NumberUuid
{

    protected static $instance = null;

    /**
     * @var \Redis
     */
    protected $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function disconnect() {
        $this->client = null;
        self::$instance = null;
    }

    public static function instance(){
        if(!self::$instance){
            self::$instance = new self(Redis::connection());
        }
        return self::$instance;
    }

    /**
     * 生成随机9位唯一数
     * @return int|string
     */
    public function get_uuid_number()
    {
        $i = 0;
        $number_uuid = '-1';
        while (true) {
            if ($i > 1000) {
                break;
            }
            $number_uuid = mt_rand(800000000, 999999999);
            $filed = substr($number_uuid, 0, 5);
            $key = "uuidNumber:$filed";
            //分区存放以便快速获取
            $result = $this->client->sIsMember($key, $number_uuid);
            if (!$result) {
                $push = $this->addUuidNumber($key, $number_uuid);
                if ($push) {
                    break;
                }
            }
            $i++;
        }
        return $number_uuid;
    }
    /**
     * 把用户名缓存至redis
     * @param $key
     * @param $number_uuid
     * @return mixed
     */
    public function addUuidNumber($key, $number_uuid)
    {
        if (empty($key)) {
            $filed = substr($number_uuid, 0, 5);
            $key = "uuidNumber:$filed";
        }
        return $this->client->sAdd($key, $number_uuid);
    }
    /**
     * 释放用户名
     * @param $key
     * @param $number_uuid
     * @return mixed
     */
    public function delUuidNumber($key, $number_uuid)
    {
        if (empty($key)) {
            $filed = substr($number_uuid, 0, 5);
            $key = "uuidNumber:$filed";
        }
        return $this->client->sRem($key, $number_uuid);
    }

}