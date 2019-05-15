<?php

namespace App\Console\Commands\FixData;

use App\Models\Auth\Company;
use App\Models\Auth\CompanyUser;
use App\Models\Auth\User;
use App\Models\Auth\VendorCompany;
use App\Models\Transport\TransportMain;
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
        $company = Company::create([
            'company_name' => '长江智链',
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
            CompanyUser::create([
                'company_id' => $company->id,
                'user_id' => $user->id,
                'company_type' => CompanyUser::COMPANY_TYPE_MAIN,
                'status' => 1
            ]);
            if ($user->id >=4 ) {
                $user->syncRoles($role);
            }
        }

        $admin = Role::find(1);
        $admin->givePermissionTo(Permission::all());

        $transportList = TransportMain::get();
        foreach ($transportList as $main) {
            if (isset($main->transport_goods['transport_vendor_company'])) {
                $exist = VendorCompany::where('company_name',$main->transport_goods['transport_vendor_company'])->where('company_id',$company->id)->first();
                if (!$exist) {
                    VendorCompany::create([
                        'company_name' => $main->transport_goods['transport_vendor_company'],
                        'company_id' => $company->id
                    ]);
                }
            }
        }
    }
}
