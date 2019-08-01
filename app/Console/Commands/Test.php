<?php

namespace App\Console\Commands;

use App\Jobs\SendPhoneMessage;
use App\Models\Auth\Company;
use App\Models\Auth\Tenant;
use App\Models\Auth\VendorCompany;
use App\Models\Transport\TransportMain;
use App\Services\RateLimiter;
use App\Services\Registrar;
use App\Third\AliLot\Constant\ContentType;
use App\Third\AliLot\Constant\HttpHeader;
use App\Third\AliLot\Constant\SystemHeader;
use App\Third\AliLot\Service;
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
        $tenant = Tenant::find(1);
        $service = new Service();
        $res = $service->getUserInfo($tenant->tenant_id,$tenant->app_id,284);
        var_dump($res);
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
