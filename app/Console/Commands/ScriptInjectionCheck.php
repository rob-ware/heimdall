<?php

namespace App\Console\Commands;

use stdClass;
use App\Models\ScriptInjectionRisk;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:script-injection-check {mode?}')]
#[Description('Checks for risks of script injection')]
class ScriptInjectionCheck extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $mode = $this->argument('mode');
        $view_dir = env('RECRUIT_VIEWS_DIR', 'views');
        $search = '{!!';
        //Initialise the database table
        $redundant_records = ScriptInjectionRisk::where('id', '>', '0')->delete();
        $results = array();
        if ($mode == 'cli')
        {
            $this->info("Searching for '$search' below $view_dir!");
        }
        $results = $this->get_directory_content($view_dir,$search, $results);
        $result_count = count($results);
        if($result_count > 0)
        {
            foreach($results as $result)
            {
                $script_injection_risk = new ScriptInjectionRisk;
                $script_injection_risk->file_name = $result->file_name;
                $script_injection_risk->lines = $result->lines;
                $script_injection_risk->path = $result->path;
                $script_injection_risk->head_directory = $result->head_directory;

                $script_injection_risk->save();
            }
        }
        if($mode == 'cli')
        {
            $this->info("Found $result_count files that need checked for Script Injection!");
            $this->info("Script complete!");
        }

    }

    public function get_directory_content($directory, $search, $results)
    {
        $files = scandir($directory);
        foreach($files as $file)
        {
            if($file == '.' || $file == '..')
            {
                continue;
            }
            else
            {
                $path = realpath($directory.'/'.$file);
                if(is_dir($path))
                {
                    $results = $this->get_directory_content($path, $search, $results);
                }
                else
                {
                    if(is_file($path))
                    {
                        $content = file_get_contents($path);
                        if(stripos($content, $search) !== false)
                        {
                            $obj = new stdClass;
                            $obj->file_name = $file;
                            $obj->path = $path;
                            $prefix = env('RECRUIT_VIEWS_DIR', 'views');
                            $prefix = $prefix."/";
                            $relative_path_string =  preg_replace('/^' . preg_quote($prefix, '/') . '/', '', $path);
                            $relative_path = explode('/', $relative_path_string);
                            $obj->head_directory = $relative_path[0];

                            $lines = array();
                            $line_number = 0;
                            $file_handler = fopen($path, "r");
                            while(( $line = fgets($file_handler, 4096)) !== false)
                            {
                                $line_number++;
                                if(stripos($line, $search) !== false)
                                {
                                    array_push($lines, $line_number);
                                }
                            }
                            $obj->lines = serialize($lines);


                            $results[] = $obj;
                        }
                    }
                }

            }

        }
        return $results;
    }

}
