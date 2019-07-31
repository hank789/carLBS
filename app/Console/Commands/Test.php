<?php

namespace App\Console\Commands;

use App\Jobs\SendPhoneMessage;
use App\Models\Auth\Company;
use App\Models\Auth\VendorCompany;
use App\Models\Transport\TransportMain;
use App\Services\RateLimiter;
use App\Services\Registrar;
use App\Third\AliLot\Constant\ContentType;
use App\Third\AliLot\Constant\HttpHeader;
use App\Third\AliLot\Constant\SystemHeader;
use App\Third\AliLot\Util\SignUtil;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试专用';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $signature = 'W9SwS3LKCKMMnCZtZ4IVUNsf0VKCt5ltBOTBVIPC0gQ=';
        $headers = [
            HttpHeader::HTTP_HEADER_CONTENT_TYPE => ContentType::CONTENT_TYPE_FORM,
            //HttpHeader::HTTP_HEADER_ACCEPT => ContentType::CONTENT_TYPE_JSON
        ];
        $body = [
            'appType' => 'PRODUCTION',
            'appId' => '2109aaa5da3b47aaa83c97e56ddb5623',
            'tenantId' => '5D8AB8E01B024D11A5FA9603841034E9',
            'id' => '7a06e9f9-7e43-4044-93b2-157c8c508afb'
        ];
        $signHeader = [SystemHeader::X_CA_TIMESTAMP];
        //var_dump(config('aliyun.lotSecret'));
        $d = SignUtil::Sign('/api/saas/createInstance','POST',config('aliyun.lotSecret'),$headers,null,$body,$signHeader);
        var_dump($d);
        return;
        $appName = '车百讯';
        (new SendPhoneMessage('15050368286',['code' => '846770047','minutes'=>10],'notify_transport_start_soon',$appName))->handle();
        return;
        $list = TransportMain::orderBy('id','asc')->get();
        foreach ($list as $main) {
            RateLimiter::instance()->hSet('vendor_company_info',$main->vendor_company_id,$main->transport_contact_vendor_people.';'.$main->transport_contact_vendor_phone);
            RateLimiter::instance()->hSet('contact_people_info',$main->transport_contact_people,$main->transport_contact_phone);
        }
        return;
        $permissions = ['账户管理'];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        return;
        $permission = Permission::find(1);
        $permission->name = '后台登陆';
        $permission->save();
        $role = Role::find(2);
        $role->name = '行程管理员';
        $role->save();
        $permissions = ['行程管理','在线车辆','司机管理'];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        $admin = Role::find(1);
        $admin->givePermissionTo(Permission::all());

        return;
        $registrar = new Registrar();
        for ($i=1;$i<=100;$i++) {
            $registrar->create([
                'name' => '手机用户'.rand(100000,999999),
                'mobile' => '150'.rand(10000000,99999999),
                'gender' => 0,
                'status' => 1,
                'visit_ip' => '127.0.0.1'
            ]);
        }
    }
}
