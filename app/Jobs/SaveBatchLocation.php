<?php

namespace App\Jobs;

use App\Services\BaiduTrace;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class SaveBatchLocation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 2;

    public $entity_name;

    public $data;



    public function __construct($entity_name, array $positionList)
    {
        $this->entity_name = $entity_name;
        $this->data = $positionList;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        BaiduTrace::instance()->trackBatch($this->entity_name,$this->data);
    }
}
