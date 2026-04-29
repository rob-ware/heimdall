<?php

namespace App\Console\Commands;

use stdClass;
use App\Models\ServerWatch;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use App\Mail\HighCpuLoad;
use App\Mail\HighHttpsLoad;
use App\Mail\HighRamLoad;
use App\Mail\HighSshLoad;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;

#[Signature('app:server-load  {mode?}')]
#[Description('Checks Server CPU loading')]
class ServerLoad extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $mode = $this->argument('mode');
        $environment = env('APP_ENV', 'local');
        $cpuUsage = 0;
        $cpuUsage = floatval($cpuUsage);
        $environment = env('APP_ENV', 'local');
        $mail_enabled = env('MAIL_ENABLED', 'no');
        $max_cpu = env('MAX_CPU', '90.0');
        $max_ram = env('MAX_RAM', '90');
        $max_https = env('MAX_HTTPS', '20');
        $max_ssh = env('MAX_SSH', '2');
        if($environment == 'local')
        {
            $cpu_idle = shell_exec("top -a -l 1 | grep 'CPU usage'| awk '{print $7}'");
            $cpu_idle = (float) substr($cpu_idle, 0, -2);
            $cpu_percentage = 100.0 - $cpu_idle;


            $ram_usage = shell_exec("top -a -l 1 | grep 'PhysMem'| awk '{print $2, $6}'");
            $ram_usage = substr($ram_usage , 0, -2);
            $ram_segments = explode(' ', $ram_usage);
            $ram_total = substr($ram_segments[0] , 0, -1);
            $ram_used = $ram_segments[1];
            //Convert Mb to Gb
            $ram_used = $ram_used / 1000;
            if($ram_total > 0)
            {
                $ram_percentage = $ram_used / $ram_total * 100;
            }
            else
            {
                $ram_percentage = 0;
            }
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
            $cpu_percentage = $cpu_percentage / 4;

            $ram_usage = shell_exec("top -b -n 1 | grep 'Mem'| grep -v 'Swap' | awk '{print $4, $8}'");
            $ram_segments = explode(' ', $ram_usage);
            $ram_percentage =  $ram_segments[1] /  $ram_segments[0] * 100;
            $ram_percentage = round($ram_percentage,1);

            $https_connections = shell_exec("ss -nt state established  | awk '{print $4}' | grep ':443' | wc -l");
            $https_connections = trim($https_connections);
            $ssh_connections = shell_exec("ss -nt state established  | awk '{print $3}' | grep ':22' | wc -l");
            $ssh_connections = trim($ssh_connections);
        }

        if($cpu_percentage > $max_cpu)
        {
            //log and email a warning, with any connected server information
            $connected_ips = $this->get_connected_ips($environment);
            if($mail_enabled == 'yes')
            {
                Mail::to('r.ware@ulster.ac.uk')->send(new HighCpuLoad($connected_ips));
                if($mode == 'cli')
                {
                    $this->info('Emailing out warning on CPU load!');
                }
            }

        }

        if($ram_percentage > $max_ram)
        {
            //log and email a warning, with any connected server information
            $connected_ips = $this->get_connected_ips($environment);
            if($mail_enabled == 'yes')
            {
                Mail::to('r.ware@ulster.ac.uk')->send(new HighRamLoad($connected_ips));
            }
            if($mode == 'cli')
            {
                $this->info('Emailing out warning on RAM load!');
            }

        }

        if($https_connections > $max_https)
        {
            //log and email a warning, with any connected server information
            $connected_ips = $this->get_connected_ips($environment);
            if($mail_enabled == 'yes')
            {
                Mail::to('r.ware@ulster.ac.uk')->send(new HighHttpsLoad($connected_ips));
            }
            if($mode == 'cli')
            {
                $this->info('Emailing out warning on HTTPS connections!');
            }
        }

        if($ssh_connections > $max_ssh)
        {
            //log and email a warning, with any connected server information
            $connected_ips = $this->get_connected_ips($environment);
            if($mail_enabled == 'yes')
            {
                Mail::to('r.ware@ulster.ac.uk')->send(new HighSshLoad($connected_ips));
            }
            if($mode == 'cli')
            {
                $this->info('Emailing out warning of on SSH connections!');
            }

        }

        $record_count = ServerWatch::count();
        if($record_count > 5)
        {
            //Delete the oldest record
            $redundant_record = ServerWatch::orderBy('id')->first()->delete();
        }

        $server_watch = new ServerWatch;
        $server_watch->cpu_percentage = $cpu_percentage;
        $server_watch->ram_percentage = $ram_percentage;
        $server_watch->https_connections = $https_connections;
        $server_watch->ssh_connections = $ssh_connections;

        $server_watch->save();

    }
    public function get_connected_ips($environment)
    {
        if($environment == 'local')
        {
            $connected_ip_scan = "arp -a";
        }
        else
        {
            $connected_ip_scan = "ip neigh";
        }

        $fp = popen ($connected_ip_scan, "r");
        $connected_ips = array();
        while ($rec = fgets($fp))
        {
            $ip_address = new stdClass;
            $ip_address->connnected_server = trim($rec);
            $connected_ips[] =  $ip_address;
        }
        if(count($connected_ips) > 1)
        {
            $ip_address = new stdClass;
            $ip_address->connnected_server = "Currently there are no inbound connections!";
            $connected_ips[] =  $ip_address;
        }

        return $connected_ips;
    }
}
