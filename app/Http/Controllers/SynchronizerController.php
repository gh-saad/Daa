<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


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
                        "message" => $response_data["APISTATUS"]["Message"],
                    ];
                }
            }
            $result = [
                "status" => "ok",
            ];
        }else{
            $result = [
                "status" => "faild",
                "message" => "Authentication failed"
            ];
        }
        return json_encode($result);
    }

    function get_members(){
        $db_table = "users";
        $auth_response = $this->auth();
        $auth_response = json_decode($auth_response);
        if($auth_response->status == "ok"){
            $response = Http::post(env("AAA_MEMBERS_URL"), [
                "emailId" => env("AAA_USERNAME"),
                "jwtToken" => session()->get("AAA_jwtToken"),
                "memberType" => 1,
                "fromDate" => "2024-01-01",
                "toDate" => "2024-03-29" 
            ]);
            if ($response->successful()) {
                $response_data = $response->json();
                foreach ($response_data["ENTITY_LIST"]["memberDetailList"] as $member){
                    $member_data = [
                        "password" => Hash::make('1234'), # password
                        "is_enable_login" => 0, # disable login for now
                        "created_by" => 2, # admin user id
                        "workspace_id" => 1, # workspace id
                        "active_workspace" => 1, # active workspace id
                        "member_id" => $member[0], # member_id
                        "name" => $member[1]." ".$member[2], # member name
                        "role" => $member[3], # member role
                        "email" => $member[4], # email address of the member
                        "company_name" => $member[5], # company name
                        "contact_no" => $member[6], # contact number of the member
                        "address" => $member[7], # address of the member
                        "address1" => $member[8], # address again of the member
                        "country" => $member[9], # country of the member
                        "state" => $member[10], # state of the member
                        "city" => $member[11], # city of the member
                        "gender" => $member[12], # gender of the member
                        "balance_privilege_point" => $member[13], # balance privilege point
                        "type" =>  $member[14], # type of member
                        "plan_expire_date" => $member[15], # account renewal date
                        "created_at" => now(),
                        "updated_at" => now() 
                    ];
                   
                    $db_member = DB::table($db_table)->where("email", $member[4])->first();
                    if ($db_member){
                        // update
                        unset($member_data["created_at"]);
                        unset($member_data["password"]);
                        DB::table($db_table)
                        ->where("id", $db_member->id)
                        ->update($member_data);
                    }else{
                        // create
                        DB::table($db_table)->insert($member_data);
                    }
                }
                
            }else{
                $response_data = $response->json();
                if($response_data["APISTATUS"]["status"] == "401"){
                    $result = [
                        "status" => "ok",
                        "message" => $response_data["APISTATUS"]["Message"],
                    ];
                }
            }
            $result = [
                "status" => "ok",
            ];
        }else{
            $result = [
                "status" => "failed",
                "message" => "Authentication failed"
            ];
        }
        return json_encode($result);
    }

    function get_data(){
        $member_status = $this->get_members();
        $vehicle_status = $this->get_vehicles();
        $member_status = json_decode($member_status);
        $vehicle_status = json_decode($vehicle_status);
        if ($member_status->status == "ok" and $vehicle_status->status == "ok") {
            $result = [
                "status" => "ok",
            ];
        }else{
            $result = [
                "status" => "failed",
                "Message" => $member_status['message'].' '.$vehicle_status['message']
            ];
        }
        return json_encode($result);

    }
}