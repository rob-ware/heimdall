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
            $cpu_usage = shell_exec("top -b -n 1 | grep '%Cpu'| awk '{print $1, $2}'");
            $ram_usage = shell_exec("top -b -n 1 | grep 'Mem'| grep -v 'Swap' | awk '{print $4, $8}'");
        }
        $cpu_details = explode(':', $cpu_usage);
        $cpu_trim = explode('u', $cpu_details[1]);
        $cpu_percentage = trim($cpu_trim[0]);
        $ram_segments = explode(' ', $ram_usage);
        $ram_percentage =  $ram_segments[1] /  $ram_segments[0] * 100;
        $ram_percentage = round($ram_percentage,1);
        printf("$cpu_percentage : $ram_percentage");

    }
}
