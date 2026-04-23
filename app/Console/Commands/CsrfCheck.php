<?php

namespace App\Console\Commands;

use stdClass;
use App\Models\CsrfRisk;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:csrf-check {mode?}')]
#[Description('Checks Laravel forms for CSRF protection')]
class CsrfCheck extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $mode = $this->argument('mode');
        $view_dir = env('RECRUIT_VIEWS_DIR', 'views');
        $form_search = 'form action';
        $csrf_search = 'csrf';
        //Initialise the database table
        $redundant_records = CsrfRisk::where('id', '>', '0')->delete();
        $results = array();
        if ($mode == 'cli')
        {
            $this->info("Searching for forms without CSRF protection below $view_dir!");
        }
        $results = $this->get_directory_content($view_dir, $form_search, $csrf_search, $results);
        $result_count = count($results);
        if($result_count > 0)
        {
            foreach($results as $result)
            {
                $csrf_injection_risk = new CsrfRisk;
                $csrf_injection_risk->file_name = $result->file_name;
                $csrf_injection_risk->path = $result->path;

                $csrf_injection_risk->save();
            }
        }
        if($mode == 'cli')
        {
            $this->info("Found $result_count files that need checked for CSRF protection!");
            $this->info("Script complete!");
        }

    }

    public function get_directory_content($directory, $form_search, $csrf_search, $results)
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
                    $results = $this->get_directory_content($path, $form_search, $csrf_search, $results);
                }
                else
                {
                    if(is_file($path))
                    {
                        $content = file_get_contents($path);
                        if(stripos($content, $form_search) !== false)
                        {
                            $form_count = 0;
                            $csrf_count = 0;
                            $file_handler = fopen($path, "r");
                            while(( $line = fgets($file_handler, 4096)) !== false)
                            {
                                if(stripos($line, $form_search) !== false)
                                {
                                    $form_count++;
                                }
                                if(stripos($line, $csrf_search) !== false)
                                {
                                    $csrf_count++;
                                }
                            }
                            if($form_count !== $csrf_count)
                            {
                                $obj = new stdClass;
                                $obj->file_name = $file;
                                $obj->path = $path;
                                $results[] = $obj;
                            }

                        }
                    }
                }

            }

        }
        return $results;
    }
}
