<?php

namespace App\Console\Commands\FixData;

use App\Models\Auth\Company;
use App\Models\Auth\CompanyRel;
use App\Models\Auth\User;
use App\Models\Transport\TransportEntity;
use App\Models\Transport\TransportMain;
use App\Models\Transport\TransportSub;
use App\Services\GeoHash;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Repositories\Backend\Auth\UserRepository;
class InitContactPeople extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixdata-init-contact-people';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '初始化收货人员数据';

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

        $role = Role::find(6);

        $transportList = TransportMain::get();
        $userRepository = new UserRepository();
        foreach ($transportList as $main) {
            if ($main->vendor_company_id) {
                try {
                    $vendorUser = $userRepository->create([
                        'first_name' => $main->transport_contact_vendor_people,
                        'last_name' => '',
                        'mobile' => $main->transport_contact_vendor_phone,
                        'password' => time(),
                        'active' => 1,
                        'confirmed' => 1,
                        'roles' => ['供应商人员'],
                        'permissions' => []
                    ]);
                    $vendorUser->company_id = $main->vendor_company_id;
                    $vendorUser->save();
                } catch (\Exception $e) {
                    $this->warn($e->getMessage());
                }
            }
            if ($main->transport_contact_phone) {
                try {
                    $distanceUser = $userRepository->create([
                        'first_name' => $main->transport_contact_people,
                        'last_name' => '',
                        'mobile' => $main->transport_contact_phone,
                        'password' => time(),
                        'active' => 1,
                        'confirmed' => 1,
                        'roles' => ['收货人员'],
                        'permissions' => []
                    ]);
                    $distanceUser->company_id = 0;
                    $distanceUser->save();
                } catch (\Exception $e) {
                    $this->warn($e->getMessage());
                }
            }
        }
        $cars = TransportEntity::get();
        foreach ($cars as $entity) {
            if (isset($entity->entity_info['lastSub']['sub_id'])) {
                $sub = TransportSub::find($entity->entity_info['lastSub']['sub_id']);
                $transportMain = $sub->transportMain;
                $distanceUser = User::where('mobile',$transportMain->transport_contact_phone)->first();
                $entity->last_contact_id = $distanceUser?$distanceUser->id:0;
            } else {
                $entity->last_contact_id = 0;
            }
            $entity->save();
        }
    }
}
