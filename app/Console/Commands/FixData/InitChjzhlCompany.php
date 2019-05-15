<?php

namespace App\Console\Commands\FixData;

use App\Models\Auth\Company;
use App\Models\Auth\CompanyRel;
use App\Models\Auth\User;
use App\Models\Transport\TransportEntity;
use App\Models\Transport\TransportMain;
use App\Models\Transport\TransportSub;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InitChjzhlCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixdata-init-chjzhl-company';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '初始化长江智链公司数据';

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
        $companyMain = Company::create([
            'company_name' => '中讯智慧',
            'company_type' => Company::COMPANY_TYPE_MAIN,
            'company_logo' => '',
            'status' => 1
        ]);
        $company = Company::create([
            'company_name' => '长江智链',
            'company_type' => Company::COMPANY_TYPE_MAIN,
            'company_logo' => '',
            'status' => 1
        ]);
        $role = Role::create([
            'name' => '系统管理员',
            'guard_name' => 'web'
        ]);
        $role2 = Role::create([
            'name' => '供应商人员',
            'guard_name' => 'web'
        ]);
        $permissions = ['供应商管理','公司管理','账户管理'];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $users = User::all();
        foreach ($users as $user) {
            if ($user->id >=4 ) {
                $user->company_id = $company->id;
                $user->save();
                $user->syncRoles($role);
            } else {
                $user->company_id = $companyMain->id;
                $user->save();
            }
        }

        $admin = Role::find(1);
        $admin->givePermissionTo(Permission::all());

        $transportList = TransportMain::get();
        foreach ($transportList as $main) {
            if (isset($main->transport_goods['transport_vendor_company'])) {
                $vendor = Company::where('company_name',$main->transport_goods['transport_vendor_company'])->first();
                if (!$vendor) {
                    $vendor = Company::create([
                        'company_name' => $main->transport_goods['transport_vendor_company'],
                        'company_type' => Company::COMPANY_TYPE_VENDOR,
                    ]);
                    CompanyRel::create([
                        'company_id' => $company->id,
                        'vendor_id' => $vendor->id
                    ]);
                }
                $main->company_id = $company->id;
                $main->vendor_company_id = $vendor->id;
                $main->save();
            } else {
                $main->company_id = $company->id;
                $main->vendor_company_id = 0;
                $main->save();
            }
        }
        $cars = TransportEntity::get();
        foreach ($cars as $entity) {
            if (isset($entity->entity_info['lastSub']['sub_id'])) {
                $sub = TransportSub::find($entity->entity_info['lastSub']['sub_id']);
                $transportMain = $sub->transportMain;
                $entity->last_company_id = $transportMain->company_id;
                $entity->last_vendor_company_id = $transportMain->vendor_company_id;
                $entity->last_sub_status = $sub->transport_status;
                $entity->save();
            } else {
                $entity->last_company_id = $company->id;
                $entity->last_vendor_company_id = 0;
                $entity->last_sub_status = 2;
                $entity->save();
            }
        }
    }
}
