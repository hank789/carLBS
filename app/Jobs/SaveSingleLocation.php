<?php

namespace App\Jobs;

use App\Models\Transport\TransportSub;
use App\Services\BaiduTrace;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class SaveSingleLocation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 2;

    public $data;

    public $transport_sub_id;



    public function __construct($transport_sub_id, array $position)
    {
        $this->transport_sub_id = $transport_sub_id;
        $this->data = $position;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sub = TransportSub::find($this->transport_sub_id);
        BaiduTrace::instance()->trackSingle($sub->getEntityName(),$this->data);
    }
}
