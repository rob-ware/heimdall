<?php


use App\Models\FailedLogin;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:monitor-logins {mode?}')]
#[Description('Monitors failed logins')]
class MonitorLogins extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $mode = $this->argument('mode');
        $filename = env('LOGIN_LOG', 'logins.log');
        //Flush records older than today
        $today = $date = date('Y-m-d', time());
        $today = $today.' 00:00:00';
        $redundant_records = FailedLogin::where('timestamp', '<', $today)->delete();
        if (file_exists($filename))
        {
            if($mode == 'cli')
            {
                $this->info("The file $filename exists!");
                $this->info("Loading contents!");
            }
            $failed_logins = file($filename, FILE_IGNORE_NEW_LINES);
            $login_count = count($failed_logins);
            if($login_count < 1)
            {
                if($mode == 'cli')
                {
                    $this->info("No failed logins found!");
                    $this->info("Script complete!");
                }
            }
            else
            {
                if($mode == 'cli')
                {
                    $this->info("Found $login_count failed logins!");
                }
                foreach($failed_logins as $failed_login)
                {
                    $login_details = explode(' ', $failed_login);
                    $date_details = explode('.', $login_details[0]);
                    $login_time = str_replace('T', ' ', $date_details[0]);
                    $user = $login_details[1];
                    $ip_address = $login_details[2];
                    $protocol = $login_details[3];

                    $aborted_login = new FailedLogin;
                    $aborted_login->user = $user;
                    $aborted_login->ip_address = $ip_address;
                    $aborted_login->protocol = $protocol;
                    $aborted_login->login_time = $login_time;

                    $aborted_login->save();
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
