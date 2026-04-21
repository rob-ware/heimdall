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
        if($environment == 'local')
        {
            $process_snapshot = "arp -a | grep '$ip_address' | awk '{print $3}'";
        }
        else
        {
            $process_snapshot = "ps -aux | grep -v 'USER'";
        }

        $fp = popen ($process_snapshot, "r");
        $processes = array();
        while ($rec = fgets($fp))
        {
            $process_snapshot[] = trim($rec);
        }
        foreach($processes as $process)
        {
            $cols = split(' ', ereg_replace(' +', ' ', $process));
            if (strpos($cols[2], '.') > -1)
            {
                $cpuUsage += floatval($cols[2]);
            }
        }
        print($cpuUsage);
    }
}
