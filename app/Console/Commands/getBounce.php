<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
class getBounce extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:bounce';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get bounced emails every ten minutes';

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
        $path = "bounce-" . date('Y-m-d') . "-0000.csv";
        $file = Storage::disk('pmta')->get($path);
        shell_exec(' yes | cp /root/bounce.csv  /var/log/pmta/'.$path);
        $data = array_map('str_getcsv', explode("\n", $file));
        unset($data[0]);
        $bounces = [];
        $bouncestest = [];
        $bounceswarm = [];
        $hardbounces = [];
        $hardbouncestest = [];
        $hardbounceswarm = [];
        foreach ($data as $line) {

            if (count($line) > 9) {
  
              $jobID = explode("-",$line[10]);
  
              if (count($jobID) > 3) {
  
                $drop_id = $jobID[0];
                $mailer_id = $jobID[1];
                $interface_id = $jobID[2];
                $list_id = $jobID[3];
  
              }else {
  
                continue;
  
              }
  
              //*****if the email is a test, notif or warmup email *******
  
              if ($drop_id == "notif") {
                continue;
              }
  
              
  
                
                if ($line[1] == "hardbnc" || $line[1] == "quota-issues") {
                     if($drop_id == "test"){
                      $hardbouncestest[$mailer_id][] = $interface_id . "\n";
                     }else if($drop_id == "warmup"){
                       $hardbounceswarm[$mailer_id][] = $interface_id . "\n";
                     }else{
                      $hardbounces[$drop_id . "-" . $mailer_id][] = $list_id . "," . $interface_id . "," . $line[5] . "\n";
                     }
                  
  
  
                }else{
                  if($drop_id == "test"){
                    $bouncestest[$mailer_id][] = $interface_id . "\n";
                   }else if($drop_id == "warmup"){
                     $bounceswarm[$mailer_id][] = $interface_id . "\n";
                   }else{
                    $bounces[$drop_id . "-" . $mailer_id][] = $list_id . "," . $interface_id . "," . $line[5] . "\n";
                   }

                }
            }
  
          }
          if (!is_dir(storage_path() . '/app/Bounce')) {
            // dir doesn't exist, make it
            mkdir(storage_path() . '/app/Bounce');
          }
          if (!is_dir(storage_path() . '/app/BounceTest')) {
            // dir doesn't exist, make it
            mkdir(storage_path() . '/app/BounceTest');
          }
          if (!is_dir(storage_path() . '/app/BounceWarm')) {
            // dir doesn't exist, make it
            mkdir(storage_path() . '/app/BounceWarm');
          }
          if (!is_dir(storage_path() . '/app/HardBounce')) {
            // dir doesn't exist, make it
            mkdir(storage_path() . '/app/HardBounce');
          }
          if (!is_dir(storage_path() . '/app/HardBounceTest')) {
            // dir doesn't exist, make it
            mkdir(storage_path() . '/app/HardBounceTest');
          }
          if (!is_dir(storage_path() . '/app/HardBounceWarm')) {
            // dir doesn't exist, make it
            mkdir(storage_path() . '/app/HardBounceWarm');
          }
          foreach ($hardbounces as $key => $value) {
            file_put_contents(storage_path() . "/app/HardBounce/" . $key . ".txt", $value);
          }
          foreach ($hardbouncestest as $key => $value) {
            file_put_contents(storage_path() . "/app/HardBounceTest/" . $key . ".txt", $value);
          }
          foreach ($hardbounceswarm as $key => $value) {
            file_put_contents(storage_path() . "/app/HardBounceWarm/" . $key . ".txt", $value);
          }
          foreach ($bounces as $key => $value) {
            file_put_contents(storage_path() . "/app/Bounce/" . $key . ".txt", $value);
          }
          foreach ($bouncestest as $key => $value) {
            file_put_contents(storage_path() . "/app/BounceTest/" . $key . ".txt", $value);
          }
          foreach ($bounceswarm as $key => $value) {
            file_put_contents(storage_path() . "/app/BounceWarm/" . $key . ".txt", $value);
          }
          $hardbounceFiles = File::files(storage_path('app/HardBounce/'));
          foreach ($hardbounceFiles as $file) {
                Http::attach($file->getFilenameWithoutExtension(),$file->getContents())->post("http://178.238.231.176/api/gethardbounce");
                unlink($file);
            
          }
          $hardbounceFilesTest = File::files(storage_path('app/HardBounceTest/'));
          foreach ($hardbounceFilesTest as $file) {
                Http::attach($file->getFilenameWithoutExtension(),$file->getContents())->post("http://178.238.231.176/api/gethardbouncetest");
                unlink($file);
            
          }
          $hardbounceFilesWarm = File::files(storage_path('app/HardBounceWarm/'));
          foreach ($hardbounceFilesWarm as $file) {
                Http::attach($file->getFilenameWithoutExtension(),$file->getContents())->post("http://178.238.231.176/api/gethardbouncewarm");
                unlink($file);
            
          }
          $bounceFiles = File::files(storage_path('app/Bounce/'));
          foreach ($bounceFiles as $file) {
                Http::attach($file->getFilenameWithoutExtension(),$file->getContents())->post("http://178.238.231.176/api/getbounce");
                unlink($file);
            
          }
          $bounceFilesTest = File::files(storage_path('app/BounceTest/'));
          foreach ($bounceFilesTest as $file) {
                Http::attach($file->getFilenameWithoutExtension(),$file->getContents())->post("http://178.238.231.176/api/getbouncetest");
                unlink($file);
            
          }
          $bounceFilesWarm = File::files(storage_path('app/BounceWarm/'));
          foreach ($bounceFilesWarm as $file) {
                Http::attach($file->getFilenameWithoutExtension(),$file->getContents())->post("http://178.238.231.176/api/getbouncewarm");
                unlink($file);
            
          }
         
    }
}
