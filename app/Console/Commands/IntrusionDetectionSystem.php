<?php

namespace App\Console\Commands;

use App\Models\AuthorisedVisitors;
use App\Models\CurrentVisitors;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Mail\MultipleMacsFound;
use App\Mail\UnauthorisedVisitorFound;
use Illuminate\Support\Facades\Mail;

#[Signature('app:intrusion-detection-system  {mode?}')]
#[Description('Checks OS users for authorisation')]
class IntrusionDetectionSystem extends Command
{
    /**
     * Detects logins from an unauthorised MAC address
     * Script runs on console scheduler every 2 minutes
     */
    public function handle()
    {
        //Only keep today's records for the moment as there is an e-Mail trail
        $today = $date = date('Y-m-d', time());
        $today = $today.' 00:00:00';
        $previous_scan = date('Y-m-d H:i:s', strtotime("-2 minutes"));
        $this->info("Script previously ran at $previous_scan!");
        $redundant_records = CurrentVisitors::where('login_time', '<', $today)->delete();
        //Check for CLI debug mode
        $mode = $this->argument('mode');

        $environment = env('APP_ENV', 'local');
        if($environment == 'local')
        {
            $current_visitors_search = "last | grep 'still logged in' | grep '.'  | awk '{print $1, $2, $4, $5, $6}'";
        }
        else
        {
            $current_visitors_search = "last | grep 'still logged in' |  grep -v 'ansible' | grep '.' | awk '{print $1, $3, $5, $6, $7}'";
        }
        $fp = popen ($current_visitors_search, "r");
        $current_visitors = array();
        while ($rec = fgets($fp))
        {
            $current_visitors[] = trim($rec);
        }

        if(count($current_visitors) > 0)
        {
            foreach($current_visitors as $current_visitor)
            {
                $visitor_details = explode(' ',$current_visitor);
                $name = $visitor_details[0];
                $ip_address = $visitor_details[1];
                $year = date('Y');
                if($environment == 'local')
                {
                    $month = $visitor_details[3];
                    $day = $visitor_details[2];
                    $login = $visitor_details[4];
                }
                else
                {
                    $month = $visitor_details[2];
                    $day = $visitor_details[3];
                    $login = $visitor_details[4];
                }
                $login_time = "$year-$month-$day $login:00";
                //Check if we have already logged this visitor
                $existing_login_record = CurrentVisitors::where('name', $name)
                                                            ->where('ip_address', $ip_address)
                                                            ->where('login_time', '<', $previous_scan)
                                                            ->first();
                if($existing_login_record)
                {
                    if ($this->option('cli'))
                    {
                        $this->info("Found an earlier login for user $name!");
                    }
                    continue;
                }
                if($environment == 'local')
                {
                    $visitor_mac_search = "arp -a | grep '$ip_address' | awk '{print $3}'";
                }
                else
                {
                    $visitor_mac_search = "ip neigh | grep '$ip_address' | awk '{print $5}'";
                }

                $fp = popen ($visitor_mac_search, "r");
                $visitor_macs = array();
                while ($rec = fgets($fp))
                {
                    $visitor_macs[] = trim($rec);
                }
                //Handle an intruder impersonating an existing visitor
                if(count($visitor_macs) > 1)
                {
                    //log and email a warning
                    Mail::to('r.ware@ulster.ac.uk')->send(new MultipleMacsFound());
                    if ($this->option('cli'))
                    {
                        $this->info('Emailing out warning of multiple MAC addresses!');
                    }
                }
                if(count($visitor_macs) === 1)
                {
                    $mac_address = $visitor_macs[0];
                    //Check if they are in the authorised list of MAC addresses
                    $authorised_visitor = AuthorisedVisitors::where('mac_address', $mac_address)
                        ->where('name', $name)
                        ->where('ip_address', $ip_address)
                        ->first();
                    if(!$authorised_visitor)
                    {
                        //email a warning
                        $authorised = 'no';
                        //log and email a warning
                        Mail::to('r.ware@ulster.ac.uk')->send(new UnauthorisedVisitorFound());
                        if($mode == 'cli')
                        {
                            $this->info('Emailing out warning of an intruder!');
                        }

                    }
                    else
                    {
                        $authorised = 'yes';
                    }
                    $new_visitor = new CurrentVisitors;
                    $new_visitor->name = $name;
                    $new_visitor->ip_address = $ip_address;
                    $new_visitor->mac_address = $mac_address;
                    $new_visitor->authorised = $authorised;
                    $new_visitor->login_time = $login_time;
                    $new_visitor->save();
                }



            }
        }


    }
}
