<?php

namespace App\Console\Commands;

use App\Mail\UnauthorisedRootUser;
use App\Models\CurrentVisitors;
use App\Models\SudoEvent;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('app:monitor-root-access {mode?}')]
#[Description('Checks for suspicious root access')]
class MonitorRootAccess extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $mode = $this->argument('mode');
        $filename = env('SUDOERS_LOG', 'sudoers.log');
        $flagged_users = array();
        if (file_exists($filename))
        {
            if($mode == 'cli')
            {
                $this->info("The file $filename exists!");
                $this->info("Loading contents!");
            }
            $sudo_events = file($filename, FILE_IGNORE_NEW_LINES);
            $sudo_count = count($sudo_events);
            if($sudo_count < 1)
            {
                if($mode == 'cli')
                {
                    $this->info("No current root access found!");
                    $this->info("Script complete!");
                }
            }
            else
            {
                if($mode == 'cli')
                {
                    $this->info("Found $sudo_count ROOT logins in the current interval!");
                }
                $today = $date = date('Y-m-d', time());
                $today = $today.' 00:00:00';
                //Flush records older than today
                $redundant_records = SudoEvent::where('timestamp', '>', $today)->delete();
                foreach($sudo_events as $sudo_event)
                {
                    $event_details = explode(' ', $sudo_event);
                    $login_details = explode('.', $event_details[0]);
                    $timestamp = str_replace('T', ' ', $login_details[0]);
                    $user_details = explode('(', $event_details[10]);
                    $user = $user_details[0];

                    $sudo_record = new SudoEvent;

                    $sudo_record->user = $user;
                    $sudo_record->timestamp = $timestamp;

                    $sudo_record->save();

                    //Check the root user`s authorisation
                    $current_visitors = CurrentVisitors::where('name', $user)->get();
                    if($current_visitors->count() > 0);
                    {
                        foreach($current_visitors as $current_visitor)
                        {
                            if($current_visitor->authorised == 'no')
                            {
                                if(!in_array($current_visitor->name, $flagged_users))
                                {
                                    Mail::to('r.ware@ulster.ac.uk')->send(new UnauthorisedRootUser());
                                    if($mode == 'cli')
                                    {
                                        $this->info('Emailing out warning of invalid ROOT access!');
                                    }
                                    $flagged_users[] = $current_visitor->name;
                                }

                            }
                        }
                    }


                }


            }
        }
        else
        {
            if($mode == 'cli')
            {
                $this->info("The file $filename does not exist!");
                $this->info("Aborting script!");
            }
        }
    }
}
