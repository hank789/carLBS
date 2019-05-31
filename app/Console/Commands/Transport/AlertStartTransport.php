<?php

namespace App\Console\Commands\Transport;

use App\Jobs\SendPhoneMessage;
use App\Models\Auth\Company;
use App\Models\Transport\TransportMain;
use App\Models\Transport\TransportSub;
use Illuminate\Console\Command;

class AlertStartTransport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alert:transport:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '提醒司机开始行程';

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
        $mainList = TransportMain::where('transport_status',TransportMain::TRANSPORT_STATUS_PROCESSING)->get();
        foreach ($mainList as $main) {
            if (isset($main->transport_goods['transport_phone_list'])) {
                $phoneArr = explode(',',$main->transport_goods['transport_phone_list']);
            } else {
                $phoneArr = [];
            }
            $company = Company::find($main->company_id);
            $appName = $company->getAppname();
            $subs = TransportSub::where('transport_main_id',$main->id)->get();
            if (strtotime($main->transport_start_time) <= strtotime('-7 days')) {
                if (count($phoneArr) <= $subs->count()) {
                    $main->transport_status = TransportMain::TRANSPORT_STATUS_FINISH;
                } else {
                    $main->transport_status = TransportMain::TRANSPORT_STATUS_OVERTIME_FINISH;
                }
                $main->save();
            } elseif (strtotime($main->transport_start_time) >= strtotime('-2 days')) {
                if (count($phoneArr) <= $subs->count()) continue;
                $subPhoneArr = [];
                foreach ($subs as $sub) {
                    $subPhoneArr[] = $sub->apiUser->mobile;
                }
                foreach ($phoneArr as $phone) {
                    if (in_array($phone,$subPhoneArr)) continue;
                    dispatch(new SendPhoneMessage($phone,['code' => $main->transport_number],'notify_transport_start',$appName));
                }
            }
        }
    }
}
