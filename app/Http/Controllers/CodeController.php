<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\SqlInjectionRisk;
use App\Models\CsrfRisk;
use App\Models\ScriptInjectionRisk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class CodeController extends Controller
{
    //
    public function show()
    {
        //Script injection dashboard
        $script_injection_categories = ScriptInjectionRisk::groupBy('head_directory')
                                        ->where('head_directory', 'not like', '%.blade.php')
                                        ->pluck('head_directory');
        $script_category_text = env('RECRUIT_VIEWS_DIR', 'views');
        $script_category_values = '0';
        if($script_injection_categories)
        {
            //XSS Dashboard
            $script_category_text = '';
            $script_category_values = '';
            $script_categories = array();
            foreach($script_injection_categories as $script_injection_category)
            {
                $script_category_text = $script_category_text."'$script_injection_category',";
                $script_category_count = ScriptInjectionRisk::where('head_directory', $script_injection_category)->count();
                $script_category_values = $script_category_values."$script_category_count,";
            }
            //remove trailing commas
            $script_category_text = substr($script_category_text, 0, -1);
            $script_category_values = substr($script_category_values, 0, -1);
        }
        //Script file listing
        $script_files = ScriptInjectionRisk::orderBy('head_directory')->get();

        //Sql injection dashboard
        $sql_injection_categories = SqlInjectionRisk::groupBy('head_directory')
            ->pluck('head_directory');
        $sql_category_text = env('RECRUIT_VIEWS_DIR', 'views');
        $sql_category_values = '0';
        if($sql_injection_categories)
        {
            $sql_category_text = '';
            $sql_category_values = '';
            foreach($sql_injection_categories as $sql_injection_category)
            {
                $sql_category_text = $sql_category_text."'$sql_injection_category',";
                $sql_category_count = SqlInjectionRisk::where('head_directory', $sql_injection_category)->count();
                $sql_category_values = $sql_category_values."$sql_category_count,";
            }
            //remove trailing commas
            $sql_category_text = substr($sql_category_text, 0, -1);
            $sql_category_values = substr($sql_category_values, 0, -1);
        }
        //SQL file listing
        $sql_files = SqlInjectionRisk::orderBy('head_directory')->get();


        return view('code.show')
            ->with('script_category_text', $script_category_text)
            ->with('script_category_values', $script_category_values)
            ->with('script_files', $script_files)
            ->with('sql_category_text', $sql_category_text)
            ->with('sql_category_values', $sql_category_values)
            ->with('sql_files', $sql_files);
    }
}
