<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class SynchronizerController extends Controller
{
    function auth() {
        $result = [
            "status" => "ok",
        ];
        if (!session()->get("AAA_jwtToken")){

            $response = Http::post(env("AAA_AUTH_URL"), [
                "emailId" => env("AAA_USERNAME"),
                "password" => env("AAA_PASSWORD"),
            ]);
            
            if ($response->successful()) {
                $response_data = $response->json();
                session()->put("AAA_jwtToken", $response_data["ENTITY_LIST"]);
                $result = [
                    "status" => "ok",
                ];
            } else {
                $response_data = $response->json();
                if($response_data["APISTATUS"]["status"] == "401"){
                    echo $response_data["APISTATUS"]["Message"];
                }
                $result = [
                    "status" => "faild",
                ];
            }
        }
        return json_encode($result);
    }

    function get_vehicles() {
        $db_table = "product_services";
        $auth_response = $this->auth();
        $auth_response = json_decode($auth_response);
        if($auth_response->status == "ok"){
            $response = Http::post(env("AAA_VECHICLE_URL"), [
                "emailId" => env("AAA_USERNAME"),
                "jwtToken" => session()->get("AAA_jwtToken"),
                "memberType" => 1, #  Accounts
                "requestType" => 1, # Yard
                "fromDate" => "2024-01-01",
                "toDate" => "2024-03-28" 
            ]);
            if ($response->successful()) {
                $response_data = $response->json();
                foreach ($response_data["ENTITY_LIST"]["stockList"] as $vehicle){
                    $vehicle_data = [
                        "vehicle_id" => $vehicle[0], #  vehicle_id
                        "category_id" => $vehicle[1], # vehicle_type
                        "name" => $vehicle[2]." ".$vehicle[3], # make_name model_name
                        "colour" => $vehicle[4], # colour 
                        "sku" => $vehicle[5], # chasis_no
                        "fuel" => $vehicle[6], # fuel
                        "mfg_year" => $vehicle[7], # mfg_year
                        "vehicle_status" => $vehicle[8], # vehicle_status
                        "purchased_by" => $vehicle[9], # purchased_by
                        "purchased_status" => $vehicle[10], # purchased_status
                        "sale_price" => $vehicle[11], # push_price
                        "bid_no" => $vehicle[12], # bid_no
                        "bid_date" => $vehicle[13], # bid_date
                        "engine_no" => $vehicle[14], # engine_no
                        "engine_cc" => $vehicle[15], # engine_cc
                        // "description" => $vehicle[], # comments
                        "quantity" => 1,
                        "sale_type" => $vehicle[16], # sale_type
                        "type"=> "product",
                        "sale_chartaccount_id" => 50,
                        "expense_chartaccount_id" => 59,
                        "workspace_id" => 1,
                        "created_by" => 2,
                        "unit_id" => 1,
                        "category_id" => 1,
                        "created_at" => now(),
                        "updated_at" => now() 
                    ];

                    $db_vehicle = DB::table($db_table)->where("sku", $vehicle[5])->first();
                    if ($db_vehicle){
                        // update
                        unset($vehicle_data["created_at"]);
                        DB::table($db_table)
                        ->where("id", $db_vehicle->id)
                        ->update($vehicle_data);
                    }else{
                        // create
                        DB::table($db_table)->insert($vehicle_data);
                    }
                }
                
            }else{
                $response_data = $response->json();
                if($response_data["APISTATUS"]["status"] == "401"){
                    $result = [
                        "status" => "ok",
                        "Message" => $response_data["APISTATUS"]["Message"],
                    ];
                }
            }
            $result = [
                "status" => "ok",
            ];
        }else{
            $result = [
                "status" => "faild",
                "Message" => "Authentication failed"
            ];
        }
        return json_encode($result);
    }
}