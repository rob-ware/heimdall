<?php

namespace App\Console\Commands;

use App\Models\AuthorisedVisitors;
use App\Models\CurrentVisitors;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('app:intrusion-detection')]

#[Description('Checks OS users for authorisation')]



class IntrusionDetection extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {//Test Mail
        $wedu_email = env('WEDU_EMAIL', 'careers@ulster.ac.uk');
        $environment = env('APP_ENV', 'local')
        if ($this->option('cli'))
        {
            $this->info('Emailing out warning of multiple MAC addresses!');
        }



        //Initialise the current_visitors table
        $deleted_visitors = CurrentVisitors::where('id', '>', '0')->delete();
        if($environment == 'local')
        {
            $current_visitors_search = "last | grep 'still logged in' | grep '.' | awk '{print $1, $2, $5, $6, $7}'";
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
        /print_r($current_visitors);die;
        if(count($current_visitors) > 0)
        {
            foreach($current_visitors as $current_visitor)
            {
                $visitor_details = explode(' ',$current_visitor);
                $name = $visitor_details[0];
                $ip_address = $visitor_details[1];
                $month = $visitor_details[2];
                $year = date('Y');
                $timestamp =  strtotime('JAN'.$year);
                $month = date('m',$timestamp);
                $day = $visitor_details[3];
                $login = $visitor_details[4];
                $login_time = "$year-$month-$day $login:00";
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
                }printf(count($visitor_macs));die;
                if(count($visitor_macs) > 1)
                {
                    //log and email a warning
                }
                if(count($visitor_macs) === 1)
                {
                    $mac_address = $visitor_macs[0];
                    $authorised_visitor = AuthorisedVisitors::where('mac_address', $mac_address)
                        ->where('name', $name)
                        ->where('ip_address', $ip_address)
                        ->first();
                    if(!$authorised_visitor)
                    {
                        //email a warning
                        $authorised = 'no';
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
