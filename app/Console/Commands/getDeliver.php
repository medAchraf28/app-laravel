<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class getDeliver extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'get:deliver';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'get the delivred files within the last 30 min';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $path = "delivered-" . date('Y-m-d') . "-0000.csv";
    $file = Storage::disk('pmta')->get($path);
    shell_exec(' yes | cp /root/deliver.csv  /var/log/pmta/'.$path);
    $data = array_map('str_getcsv', explode("\n", $file));
    unset($data[0]);
   
    $a = [];
    $t = [];
    $w = [];
    foreach ($data as $line) {
      if (!empty($line[5])) {
        $jobID = explode('-', $line[5]);
        if (count($jobID) > 3) {

          $drop_id = $jobID[0];
          $mailer_id = $jobID[1];
          $interface_id = $jobID[2];
          $id_list = $jobID[3];
          $id_email = $line[7];
        } else {

          continue;
        }
          
        if ($drop_id == "notif") {
          continue;
        }else if($drop_id == "test"){
             $t[$mailer_id][] = $interface_id."\n";
        }else if($drop_id == "warmup"){
               $w[$mailer_id][] = $interface_id."\n";
        }else{
          $a[$drop_id . "-" . $mailer_id][] = $id_email . "," . $id_list . "," . $interface_id . "," . $line[3] . "\n";
        }
        
      }
    }
    if (!is_dir(storage_path() . '/app/Delivered')) {
      // dir doesn't exist, make it
      mkdir(storage_path() . '/app/Delivered');
    }
    if (!is_dir(storage_path() . '/app/DeliveredTest')) {
      // dir doesn't exist, make it
      mkdir(storage_path() . '/app/DeliveredTest');
    }
    if (!is_dir(storage_path() . '/app/DeliveredWarm')) {
      // dir doesn't exist, make it
      mkdir(storage_path() . '/app/DeliveredWarm');
    }
    foreach ($a as $key => $value) {
      file_put_contents(storage_path() . "/app/Delivered/" . $key . ".txt", $value);
    }
    foreach ($t as $key => $value) {
      file_put_contents(storage_path() . "/app/DeliveredTest/" . $key . ".txt", $value);
    }
    foreach ($w as $key => $value) {
      file_put_contents(storage_path() . "/app/DeliveredWarm/" . $key . ".txt", $value);
    }
    $deliverFiles = File::files(storage_path('app/Delivered/'));
    foreach ($deliverFiles as $file) {
          Http::attach($file->getFilenameWithoutExtension(),$file->getContents())->post("http://178.238.231.176/api/getdeliver");
          unlink($file);
      
    }
    $deliverFilesTest = File::files(storage_path('app/DeliveredTest/'));
    foreach ($deliverFilesTest as $file) {
          Http::attach($file->getFilenameWithoutExtension(),$file->getContents())->post("http://178.238.231.176/api/getdelivertest");
          unlink($file);
      
    }
    $deliverFilesWarm = File::files(storage_path('app/DeliveredWarm/'));
    foreach ($deliverFilesWarm as $file) {
          Http::attach($file->getFilenameWithoutExtension(),$file->getContents())->post("http://178.238.231.176/api/getdeliverwarm");
          unlink($file);
      
    }
    
    
    //     Http::post("http://209.126.7.107/api/getdeliver", [
    //        'deliver_file' => $file
    //    ]);
  }
}
