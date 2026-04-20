<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:intrusion-detection {--cli}')]
#[Description('Checks OS users for authorisation')]
class IntrusionDetection extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $user_search = "last | grep 'still logged in' |  awk '{print $1, $3, $5, $6, $7}'";
        $fp = popen ($user_search, "r");
        $users = array();
        while ($rec = fgets($fp))
        {
            $users[] = trim($rec);
        }
        print_r($users);
    }
}
