<?php

namespace App\Console\Commands;

use App\Models\EnvAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:monitor-env-file  {mode?}')]
#[Description('Checks for manipulation of the Recruit .env file')]
class MonitorEnvFile extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        //Flush records older than today
        $today = $date = date('Y-m-d', time());
        $today = $today.' 00:00:00';
        $redundant_records = EnvAction::where('timestamp', '<', $today)->delete();
        $mode = $this->argument('mode');
        $filename = env('ENV_LOG', 'env.log');
        if (file_exists($filename))
        {
            if($mode == 'cli')
            {
                $this->info("The file $filename exists!");
                $this->info("Loading contents!");
            }
            $env_actions = file($filename, FILE_IGNORE_NEW_LINES);
            $action_count = count($env_actions);
            if($action_count < 1)
            {
                if($mode == 'cli')
                {
                    $this->info("No manipulation of the .env file found!");
                    $this->info("Script complete!");
                }
            }
            else
            {
                if($mode == 'cli')
                {
                    $this->info("Found $action_count actions against the Recruit .env file!");
                }
                foreach($env_actions as $env_action)
                {
                    $action_details = explode('=', $env_action);
                    $time_string = $action_details[2];
                    $time_details = explode('(', $time_string);
                    $time_data = explode('.', $time_details[1]);
                    $date = str_replace('/', '-', $time_data[0]);
                    $timestamp = date('Y-m-d H:i:s', strtotime($date));
                    $action = $action_details[3];

                    $action_record = new EnvAction;

                    $action_record->action = $action;
                    $action_record->timestamp = $timestamp;

                    $action_record->save();


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
