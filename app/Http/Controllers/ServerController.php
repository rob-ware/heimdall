<?php

namespace App\Http\Controllers;

use App\Models\CurrentVisitors;
use App\Models\EnvAction;
use App\Models\FailedLogin;
use App\Models\ServerWatch;
use App\Models\SudoEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;


class ServerController extends Controller
{
    //Provides analysis of server security

    /**
     * Show the current status of the server.
     */
    public function show()
    {
        //Current users on the server
        $visitors = CurrentVisitors::orderBy('login_time', 'desc')->get();//print_r($visitors);die;
        if($visitors)
        {
            foreach($visitors as $visitor)
            {
                $visitor->login_time = date('D H:i', strtotime($visitor->login_time));
                if($visitor->name == 'rob' || $visitor->name == 'r.ware')
                {
                    $visitor->image = 'images/rob.jpg';
                }
                elseif($visitor->name == 'casey')
                {
                    $visitor->image = 'images/casey.jpg';
                }
                else
                {
                    $visitor->image = 'images/intruder.jpg.avif';
                    $visitor->authorised  = 'no';
                    $visitor->name  = 'intruder';
                }
                if($visitor->authorised  == 'no')
                {
                    $visitor->image = 'images/intruder.jpg.avif';
                    $visitor->name  = 'intruder';
                }
            }
        }
        //.env analysis
        $env_actions = EnvAction::orderBy('timestamp', 'desc')->get();
        if($env_actions)
        {
            foreach($env_actions as $env_action)
            {
                $env_action->timestamp = date('D H:i', strtotime($env_action->timestamp));
            }
        }
        //Failed logins
        $failed_logins = FailedLogin::orderBy('login_time', 'desc')->get();
        if($env_actions)
        {
            foreach($failed_logins as $failed_login)
            {
                $failed_login->login_time = date('D H:i', strtotime( $failed_login->login_time));
            }
        }
        //Root Access
        $sudo_events = SudoEvent::orderBy('timestamp', 'desc')->get();
        if($sudo_events)
        {
            foreach($sudo_events as $sudo_event)
            {
                $sudo_event->timestamp = date('D H:i', strtotime($sudo_event->timestamp));
            }
        }
        //CPU and RAM usage
        $server_watches = ServerWatch::orderBy('created_at')->get();
        $cpu_text = '0,0,0,0,0,0';
        $ram_text = '0,0,0,0,0,0';
        if($server_watches)
        {
            $cpu_text = '';
            $ram_text = '';
            foreach($server_watches as $server_watch)
            {
                $cpu_text = $cpu_text."$server_watch->cpu_percentage,";
                $ram_text = $ram_text."$server_watch->ram_percentage,";
            }
            //remove trailing commas
            $cpu_text = substr($cpu_text, 0, -1);
            $ram_text = substr($ram_text, 0, -1);
        }
        $installed_ram = env('RAM_TOTAL', '16Gb');

        return view('server.visitors')
            ->with('visitors', $visitors)
            ->with('env_actions', $env_actions)
            ->with('failed_logins', $failed_logins)
            ->with('sudo_events', $sudo_events)
            ->with('cpu_text', $cpu_text)
            ->with('installed_ram', $installed_ram)
            ->with('ram_text', $ram_text);
    }

}
