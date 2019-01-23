<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;


class SendPhoneMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 1;

    protected $type;
    protected $phone;
    protected $params;

    /**
     * SendPhoneMessage constructor.
     * @param $phone
     * @param $params
     * @param string $type
     */
    public function __construct($phone,array $params,$type='register')
    {
        $this->phone = $phone;
        $this->params = $params;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $freeSignName = config('alidayu.sign_name');

        switch($this->type){
            case 'login':
            case 'register':
                $templateId = config('alidayu.verify_template_id');
                //$params = ['code' => $code]
                break;
            case '201802-happy-activity':
                $templateId = 'SMS_124425049';
                //$params = ['name' => $code]
                break;
            case 'invite_address_book_user':
                $templateId = 'SMS_134500006';
                break;
            case 'article_pending_alert':
                $templateId = 'SMS_143705956';
                break;
            default:
                $templateId = config('alidayu.verify_template_id');
                break;
        }
        AlibabaCloud::accessKeyClient(config('aliyun.accessKeyId'), config('aliyun.accessSecret'))
            ->regionId(config('aliyun.region')) // replace regionId as you need
            ->asGlobalClient();

        try {
            $result = AlibabaCloud::rpcRequest()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->options([
                    'query' => [
                        'PhoneNumbers' => $this->phone,
                        'SignName' => '',//短信签名
                        'TemplateCode' => '',//模板id
                        'TemplateParam' => '',//模板变量替换
                    ],
                ])
                ->request();
            print_r($result->toArray());
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }

        if ($result && $result->success == true) {
            // 发送成功～
        }else{
            //Log::error('短信验证码发送失败',[$this->type,$result, $sub_code, $sub_msg]);
        }
    }

    public static function getCacheKey($type,$phone){
        return 'sendPhoneCode:'.$type.':'.$phone;
    }
}
