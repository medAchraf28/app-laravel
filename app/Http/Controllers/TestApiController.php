<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Symfony\Component\Process\Process;

class TestApiController extends Controller
{
    public function TestSend(Request $request){
        $details = $request->details;
        $msg = $request->msg;
        Storage::disk('outside')->put($request->msg_file,  $msg);
        $response = shell_exec("nohup python /home/SendingScript.py 'test' ".escapeshellarg(json_encode($details))." ".$details[15]."");


        return response()->json([
            "message" => $response
        ], 201);
    }
    

    //****************** start sending ***************
    public function startSend(Request $request){

	$process = new Process(['sudo','service', 'pmta', 'status']);

      	$process->run();

	
	$pmta_status = $process->getOutput();

	if( str_contains($pmta_status, "Active: active (running)")){

		$details = $request->details;
        	$emails = implode("\n",$request->emails);
        	$msg = $request->msg;
        	$mode = $request->mode;
			$method = $request->method;

        	if( $mode == "warm" ){

                	Storage::disk('outside')->put("warmup_msg_".$details[0]."_".$details[2].".txt",  $msg);
                	Storage::disk('outside')->put("warmup_data_".$details[0]."_".$details[2].".txt",  $emails);

                	$command = "nohup python /home/SendingScript.py 'warm' ".escapeshellarg(json_encode($details))." ".$method." > /dev/null 2>&1 & echo $!;";

					$response = shell_exec($command);

					Storage::disk('local')->append('warmup_'.$details[0].".txt", str_replace(array("\r","\n"),"",$response)." ", null);

        	}elseif( $mode == "send" ){

				Storage::disk('outside')->put("msg_".$details[0]."_".$details[1].".txt",  $msg);
				Storage::disk('outside')->put("data_".$details[0]."_".$details[1].".txt",  $emails);
				Storage::disk('outside')->put("msg_notif_".$details[0]."_".$details[1].".txt",  $request->msg_notif);

				$command = "nohup python /home/SendingScript.py 'send' ".escapeshellarg(json_encode($details))." ".$method." > /dev/null 2>&1 & echo $!;";

				$response = shell_exec($command);

				Storage::disk('local')->append('drop_'.$details[0].".txt", str_replace(array("\r","\n"),"",$response)." ", null);

        	}

		return response()->json([
	            "pmta_status" => "active"
        	], 201);

	}else{

		return response()->json([
                    "pmta_status" => "inactive"
                ], 201);
	
	} 

    }
	public function kill_drop(Request $request){
		$pids =     Storage::disk('local')->get('drop_'.$request->drop_id.'.txt');  
		$response = shell_exec('kill '.$pids);
		  return $response;
   }
	public function kill_warmup(Request $request){
		$pids =     Storage::disk('local')->get('warmup_'.$request->warmup_id.'.txt');  
		$response = shell_exec('kill '.$pids);
		  return $response;
   }

}
