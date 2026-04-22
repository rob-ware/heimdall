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
            $cpu_usage = shell_exec("top -a -l 1 | grep 'CPU usage'| awk '{print $3}'");
            $cpu_percentage = substr($cpu_usage, 0, -2);

            $ram_usage = shell_exec("top -a -l 1 | grep 'PhysMem'| awk '{print $2}'");
            $ram_usage = substr($ram_usage , 0, -2);
            $ram_usage = (int) $ram_usage;
            $ram_percentage =  $ram_usage /  32 * 100;
            $ram_percentage = round($ram_percentage,1);

            $https_connections = shell_exec("netstat -t -l -n | grep 'tcp' | grep 'ESTABLISHED' | awk '{print $5}' | grep '.443' | wc -l");
            $https_connections = trim($https_connections);
            $ssh_connections = shell_exec("netstat -t -l -n | grep 'tcp' | grep 'ESTABLISHED' | awk '{print $4}' | grep '.22 ' | wc -l");
            $ssh_connections = trim($ssh_connections);
        }
        else
        {
            $cpu_usage = shell_exec("top -b -n 1 | grep '%Cpu'| awk '{print $1, $2}'");
            $cpu_details = explode(':', $cpu_usage);
            $cpu_trim = explode('u', $cpu_details[1]);
            $cpu_percentage = trim($cpu_trim[0]);

            $ram_usage = shell_exec("top -b -n 1 | grep 'Mem'| grep -v 'Swap' | awk '{print $4, $8}'");
            $ram_segments = explode(' ', $ram_usage);
            $ram_percentage =  $ram_segments[1] /  $ram_segments[0] * 100;
            $ram_percentage = round($ram_percentage,1);

            $https_connections = shell_exec("ss -nt state established  | awk '{print $4}' | grep ':443' | wc -l");
            $https_connections = trim($https_connections);
            $ssh_connections = shell_exec("ss -nt state established  | awk '{print $3}' | grep ':22' | wc -l");
            $ssh_connections = trim($ssh_connections);
        }

        $timestamp = date('Y-m-d H:i:s');

        printf("$cpu_percentage:::$ram_percentage:::$https_connections:::$ssh_connections:::$timestamp");

    }
}
