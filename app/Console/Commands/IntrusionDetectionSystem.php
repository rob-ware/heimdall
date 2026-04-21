<?php

namespace App\Console\Commands;

use 
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:intrusion-detection-system {--cli}')]
#[Description('Checks OS users for authorisation')]
class IntrusionDetectionSystem extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $wedu_email = env('WEDU_EMAIL', 'careers@ulster.ac.uk');
        $environment = env('APP_ENV', 'local');
        Mail::to('r.ware@ulster.ac.uk')->send(new MultipleMacsFound());
        if ($this->option('cli'))
        {
            $this->info('Emailing out warning of multiple MAC addresses!');
        }
    }
}
