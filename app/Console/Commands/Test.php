<?php

namespace App\Console\Commands;

use App\Services\Registrar;
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
