<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanServers extends Command
{
    protected $signature = 'Clean:servers';

    protected $description = 'Delete messages and data of drops and warmups';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $response = shell_exec("rm -rf /home/data* /home/msg* /home/warmup*");
        info($response);
    }
}
