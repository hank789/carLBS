<?php

namespace App\Jobs;

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

    public $entity_name;



    public function __construct($entity_name, array $position)
    {
        $this->entity_name = $entity_name;
        $this->data = $position;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        BaiduTrace::instance()->trackSingle($this->entity_name,$this->data);
    }
}
