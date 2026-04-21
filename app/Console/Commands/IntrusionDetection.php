<?php

namespace App\Console\Commands;

use App\Models\AuthorisedVisitors;
use App\Models\CurrentVisitors;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('app:intrusion-detection')]

#[Description('redundant script')]



class IntrusionDetection extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //

    }
}
