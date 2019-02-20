<?php

namespace App\Console\Commands\Baidu;

use Illuminate\Console\Command;

class YingyanQueryEntity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'baidu:yingyan:query-entity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '查询百度鹰眼设备';

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

    }
}
