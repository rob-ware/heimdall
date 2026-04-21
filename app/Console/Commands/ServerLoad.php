<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:server-load')]
#[Description('Checks Server CPU loading')]
class ServerLoad extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $cpuUsage = 0;
        $cpuUsage = floatval($cpuUsage);
        $environment = env('APP_ENV', 'local');
        if($environment == 'local')
        {

        }
        else
        {
            $cpu_usage = shell_exec("top -b -n 1 | grep '%Cpu'| awk '{print $2}'");
            $ram_usage = shell_exec("top -b -n 1 | grep 'Mem'| grep -v 'Swap' | awk '{print $4, $8}'");
        }
        printf($cpu_usage);

        $ram_segments = explode(' ', $ram_usage);print_r($ram_segments);die;

    }
}
