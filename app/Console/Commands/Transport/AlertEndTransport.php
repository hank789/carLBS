<?php

namespace App\Console\Commands\Transport;

use App\Jobs\SendPhoneMessage;
use App\Models\Auth\Company;
use App\Models\Transport\TransportMain;
use App\Models\Transport\TransportSub;
use Illuminate\Console\Command;

class AlertEndTransport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alert:transport:end';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '提醒司机结束行程';

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
        $subList = TransportSub::where('transport_status',TransportSub::TRANSPORT_STATUS_PROCESSING)->get();
        foreach ($subList as $sub) {
            $main = $sub->transportMain;
            $company = Company::find($main->company_id);
            $appName = $company->getAppname();
            if (strtotime($sub->transport_goods['transport_start_real_time']) <= strtotime('-2 days')) {
                dispatch(new SendPhoneMessage($sub->apiUser->mobile,['code' => $main->transport_number],'notify_transport_end',$appName));
            }
        }
    }
}
