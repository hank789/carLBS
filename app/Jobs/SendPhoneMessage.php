<?php

namespace App\Jobs;

use App\Events\Api\ExceptionNotify;
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
    protected $app_name;

    /**
     * SendPhoneMessage constructor.
     * @param $phone
     * @param $params
     * @param string $type
     * @param $app_name
     */
    public function __construct($phone,array $params,$type='register',$app_name='长江智链')
    {
        $this->phone = $phone;
        $this->params = $params;
        $this->type = $type;
        $this->app_name = $app_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->app_name) {
            case '车百讯':
                switch($this->type){
                    case 'login':
                    case 'backend_login':
                        $templateId = 'SMS_160200656';
                        //$params = ['name' => $code]
                        break;
                    case 'notify_transport_start':
                        $templateId = 'SMS_166485429';
                        break;
                    case 'notify_app_download':
                        $templateId = 'SMS_162733414';
                        break;
                    case 'notify_transport_cancel':
                        $templateId = 'SMS_166475502';
                        break;
                    case 'notify_transport_start_soon':
                        $templateId = 'SMS_166475490';
                        break;
                    case 'notify_transport_end':
                        $templateId = 'SMS_166779444';
                        break;
                    default:
                        break;
                }
                break;
            default:
                switch($this->type){
                    case 'login':
                    case 'backend_login':
                        $templateId = 'SMS_160200656';
                        //$params = ['name' => $code]
                        break;
                    case 'notify_transport_start':
                        $templateId = 'SMS_164826085';
                        break;
                    case 'notify_app_download':
                        $templateId = 'SMS_162733414';
                        break;
                    case 'notify_transport_cancel':
                        $templateId = 'SMS_163040048';
                        break;
                    case 'notify_transport_start_soon':
                        $templateId = 'SMS_163035174';
                        break;
                    case 'notify_transport_end':
                        $templateId = 'SMS_166867830';
                        break;
                    default:
                        break;
                }
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
                        'SignName' => '中讯智慧',//短信签名
                        'TemplateCode' => $templateId,//模板id
                        'TemplateParam' => json_encode($this->params),//模板变量替换
                    ],
                ])
                ->request();
            if ($result['Code'] != 'OK') {
                event(new ExceptionNotify('短信验证码发送失败：'.$result['Code'].';'.$result['Message']));
            }
        } catch (ClientException $e) {
            event(new ExceptionNotify('短信验证码发送失败：'.$e->getErrorMessage()));
        } catch (ServerException $e) {
            event(new ExceptionNotify('短信验证码发送失败：'.$e->getErrorMessage()));
        }
    }

    public static function getCacheKey($type,$phone){
        return 'sendPhoneCode:'.$type.':'.$phone;
    }
}
