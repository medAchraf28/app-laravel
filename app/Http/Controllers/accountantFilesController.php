<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class accountantFilesController extends Controller
{

    public function getFile(Request $request){


       $path = $request->name."-".date('Y-m-d')."-0000.csv";

        // $latest_ctime = 0;
        // $latest_filename = '';

        // $files = glob($path);

        // foreach($files as $file)
        // {
        //         if (is_file($file) && filectime($file) > $latest_ctime)
        //         {
        //                 $latest_ctime = filectime($file);
        //                 $latest_filename = $file;
        //         }
        // }
	//return str_replace("/var/log/pmta/","",$latest_filename);

         $data = Storage::disk('pmta')->download($path);
          shell_exec('sudo rm -rf /var/log/pmta/'.$path);
        return $data;



    }
}
