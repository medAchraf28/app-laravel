<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ToolsController extends Controller
{
    public function uploadimage(Request $request){
        info($request->file('image')->getClientOriginalName());
        if ($request->file('image')) {
            $imagePath = $request->file('image');
            $imageName = $imagePath->getClientOriginalName();

            $request->file('image')->storeAs("img", $imageName,'html');
        }
    }
    public function getapidomain(Request $request){
        $array = [];   
        $api_key = $request->api_key;
        $domain = $request->domain;
        info($api_key."\n".$domain);
        try
        {
            $response = Http::withOptions(['verify' => false])->get("https://endpoint.apivoid.com/spfvalidator/v1/pay-as-you-go/?key=".$api_key."&host=".$domain);
             
            $output = $response->body();
            // $curl = curl_init("https://endpoint.apivoid.com/spfvalidator/v1/pay-as-you-go/?key=".$api_key."&host=".$domain);
            // curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            // $output = curl_exec($curl);
            // info($output);
            // curl_close($curl);
            // $result = json_decode($output, true);
            
            
             $result = json_decode($output, true);

            if(array_key_exists("error", $result)){
                 if(str_contains($result["error"],"You have 0 credits remained")){

                     return response()->json(["success"=>false, "msg"=>"must increment i"]);
                 }else{
                    return response()->json(["success"=>false, "msg"=>"the error ".$result["error"]." has occured in API Key".$api_key]);
                 }
                
            } else {

            $array = [];

            $spf_records_list = isset($result["data"]["spf_records_list"])?$result["data"]["spf_records_list"]:[];

            foreach($spf_records_list as $spf_record)
            {
                if(array_key_exists("domains", $spf_record))
                {
                    $domains = $spf_record["domains"];

                    $domain_big = $spf_record["origin"];

                    foreach($domains as $domain_small)
                    {
                        $mini_array = [
                            "domain" => $domain_big,
                            "type" => "include",
                            "domain_include" => $domain_small,
                            "range" => null,
                            "created_at" => Carbon::now(),
                            "updated_at" => Carbon::now(),
                        ];

                        array_push($array, $mini_array); $mini_array = [];
                    }
                }

                if(array_key_exists("authorized_ips", $spf_record))
                {
                    if(array_key_exists("ipv4", $spf_record["authorized_ips"]))
                    {
                        $domain_small = $spf_record["origin"];

                        $ranges = $spf_record["authorized_ips"]["ipv4"];

                        foreach($ranges as $range)
                        {
                            if(str_contains($range, "/"))
                            {
                                $mini_array = [
                                    "domain" => $domain_small,
                                    "type" => "range",
                                    "domain_include" => null,
                                    "range" => $range,
                                    "created_at" => Carbon::now(),
                                    "updated_at" => Carbon::now(),
                                ];

                                array_push($array, $mini_array); $mini_array = [];
                            }
                        }
                    }
                }
            }

            
            return response()->json(["success"=>true, "array"=>$array, "msg"=>""]);
        }
        }
        catch(Exception $e)
        {
            info($e);
            return response()->json(["success"=>false, "msg"=>"Fail"]);
        }
    }
}
