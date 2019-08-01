<?php
/**
 * @author: wanghui
 * @date: 2019/7/30 4:21 PM
 * @email:    hank.HuiWang@gmail.com
 */

namespace App\Http\Controllers\Api;


use App\Events\Backend\Auth\User\UserCreated;
use App\Models\Auth\Tenant;
use App\Models\Auth\User;
use App\Third\AliLot\Constant\ContentType;
use App\Third\AliLot\Constant\HttpHeader;
use App\Third\AliLot\Util\SignUtil;
use Illuminate\Http\Request;

class SaasController extends Controller
{
    public function createInstance(Request $request) {
        \Log::info('createInstance',$request->all());
        \Log::info('createInstanceHeader',$request->header());
        $valid = $this->validSign($request,$request->getPathInfo());
        if (!$valid) {
            return response()->json([
                'code' => 203,
                'message' => '验签失败',
                'userId' => ''
            ]);
        }
        $tenant = Tenant::where('tenant_id',$request->input('tenantId'))->first();
        if (!$tenant) {
            $tenant = Tenant::create([
                'request_id' => $request->input('id'),
                'user_id' => 0,
                'app_type' => $request->input('appType'),
                'app_id' => $request->input('appId'),
                'tenant_id' => $request->input('tenantId'),
                'source' => Tenant::SOURCE_ALI,
                'status' => Tenant::STATUS_PENDING,
                'detail' => $request->all()
            ]);
        } else {
            $tenant->request_id = $request->input('id');
            $tenant->app_id = $request->input('appId');
            $tenant->detail = $request->all();
            $tenant->status = Tenant::STATUS_SUBSCRIBING;
            $tenant->save();
        }
        $user = User::where('tenant_id',$tenant->id)->first();
        if (!$user) {
            $user = User::create([
                'first_name' => 'Saas用户'.rand(10000,99999),
                'last_name' => '',
                'mobile' => time().rand(1000,9999),
                'password' => $data['password']??time(),
                'active' => 1,
                'confirmation_code' => md5(uniqid(mt_rand(), true)),
                'confirmed' => 1,
                'tenant_id' => $tenant->id,
                'company_id' => 0
            ]);
            //给系统管理员的权限
            $user->syncRoles(4);
            $tenant->user_id = $user->id;
            $tenant->save();
            event(new UserCreated($user));
        } else {
            $user->active = 1;
            $user->save();
            $tenant->user_id = $user->id;
            $tenant->save();
            User::where('company_id',$user->company_id)->update(['active'=>1]);
        }
        return response()->json([
            'code' => 200,
            'message' => 'success',
            'userId' => $user->id
        ]);

    }

    public function deleteInstance(Request $request) {
        \Log::info('deleteInstance',$request->all());
        \Log::info('deleteInstanceHeader',$request->header());
        $valid = $this->validSign($request,$request->getPathInfo());
        if (!$valid) {
            return response()->json([
                'code' => 203,
                'message' => '验签失败',
                'userId' => ''
            ]);
        }
        $tenant = Tenant::where('tenant_id',$request->input('tenantId'))->first();
        $user = User::find($request->input('userId'));
        if ($tenant) {
            $tenant->status = Tenant::STATUS_DELETED;
            $tenant->save();
        }
        if ($user) {
            $user->active = 0;
            $user->save();
            if ($user->company_id) {
                User::where('company_id',$user->company_id)->update(['active'=>0]);
            }

        }
        return response()->json([
            'code' => 200,
            'message' => 'success'
        ]);
    }

    public function getSSOUrl(Request $request) {
        \Log::info('getSSOUrl',$request->all());
        $valid = $this->validSign($request,$request->getPathInfo());
        if (!$valid) {
            return response()->json([
                'code' => 203,
                'message' => '验签失败',
                'userId' => ''
            ]);
        }
        $tenant = Tenant::where('tenant_id',$request->input('tenantId'))->first();
        //$user = User::find($request->input('userId'));
        $time = time();
        $body = [
            'checkToken' => $tenant->tenant_id,
            'time' => $time
        ];
        $headers = [
            HttpHeader::HTTP_HEADER_CONTENT_TYPE => ContentType::CONTENT_TYPE_FORM
        ];
        $sign = SignUtil::Sign('getSSOUrl','POST',config('aliyun.lotSecret'),$headers,[],$body,null);
        $ssoUrl = 'https://www.jszioe.com/login?from_source=chbx&ssoToken='.urlencode($sign).'&checkToken='.urlencode($tenant->tenant_id).'&time='.$time;
        return response()->json([
            'code' => 200,
            'message' => 'success',
            'ssoUrl' => $ssoUrl
        ]);
    }

    protected function validSign(Request $request,$path) {
        $signature = $request->header('x-ca-signature');

        $body = $request->all();
        $signHeader = null;
        $headers = [
            HttpHeader::HTTP_HEADER_CONTENT_TYPE => ContentType::CONTENT_TYPE_FORM
        ];
        $sign = SignUtil::Sign($path,'POST',config('aliyun.lotSecret'),$headers,[],$body,$signHeader);
        return $signature == $sign;
    }
}