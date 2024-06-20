<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
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
                    $vehicle_type = ucwords($vehicle[1]);
                    $vehicle_type_data = DB::table('categories')->where("name", $vehicle_type)->first();
                    if($vehicle_type_data){
                        $vehicle_type_id = $vehicle_type_data->id;
                    }else{
                        $vehicle_type_data = [
                            'name'  => $vehicle_type,
                            'type'  => 0,
                            'color' => '#000000',
                            'created_by' => 2,
                            'workspace_id' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        $vehicle_type_id = DB::table('categories')->insertGetId($vehicle_type_data); // Changed insert to insertGetId to get the inserted id directly
                    }

                    $vehicle_data = [
                        "vehicle_id" => $vehicle[0], #  vehicle_id
                        "category_id" => $vehicle_type_id, # vehicle_type
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
        $customer_table = "customers";
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
                        "plan_expire_date" => $member[15], # account renewal date
                        "created_at" => now(),
                        "updated_at" => now() 
                    ];

                    // $roleName = Role::where('id', $member[3])->value('name');
                    $member_data["type"] = 'client';

                    $db_member = DB::table($db_table)->where("email", $member[4])->first();
                    if ($db_member){
                        $customer = DB::table($customer_table)->where("user_id", $db_member->id);
                        // member already exist update it
                        unset($member_data["created_at"]);
                        unset($member_data["password"]);
                        DB::table($db_table)
                        ->where("id", $db_member->id)
                        ->update($member_data);
                        // check if customer entry exist of this member
                        if ($customer->count() != 0){
                            // update the customer entry
                            $customer_data = $customer->first();
                            $customer_data->name = $db_member->name;
                            $customer_data->email = $db_member->email;
                            $customer_data->contact = $db_member->contact_no;
                            $customer->update((array) $customer_data);
                        }else{
                            // create customer entry
                            $customer_data = [
                                "customer_id" => $db_member->id,
                                "user_id" => $db_member->id,
                                "name" => $db_member->name ?? 0,
                                "email" => $db_member->email ?? 0,
                                "password" => $db_member->password ?? 0,
                                "contact" => $db_member->contact_no ?? 0,
                                "billing_name" => $db_member->name ?? 0,
                                "billing_country" => $db_member->country ?? 0,
                                "billing_state" => $db_member->state ?? 0,
                                "billing_city" => $db_member->city ?? 0,
                                "billing_address" => $db_member->address ?? 0,
                                "billing_phone" => $db_member->contact_no ?? 0,
                                "billing_zip" => $db_member->zip_code ?? 0,
                                "shipping_name" => $db_member->name ?? 0,
                                "shipping_country" => $db_member->country ?? 0,
                                "shipping_state" => $db_member->state ?? 0,
                                "shipping_city" => $db_member->city ?? 0,
                                "shipping_address" => $db_member->address1 ?? 0,
                                "shipping_phone" => $db_member->contact_no ?? 0,
                                "shipping_zip" => $db_member->zip_code ?? 0,
                                "lang" => "en",
                                "balance" => 0.00,
                                "workspace" => $db_member->workspace_id ?? 0,
                                "created_at" => now(),
                                "updated_at" => now(),
                            ];
                            // insert customer
                            DB::table($customer_table)->insert($customer_data);
                        }
                    }else{
                        // insert member
                        $new_member = DB::table($db_table)->insert($member_data);

                        // create customer entry
                        $customer_data = [
                            "customer_id" => $new_member->id,
                            "user_id" => $new_member->id,
                            "name" => $new_member->name ?? 0,
                            "email" => $new_member->email ?? 0,
                            "password" => $new_member->password ?? 0,
                            "contact" => $new_member->contact_no ?? 0,
                            "billing_name" => $new_member->name ?? 0,
                            "billing_country" => $new_member->country ?? 0,
                            "billing_state" => $new_member->state ?? 0,
                            "billing_city" => $new_member->city ?? 0,
                            "billing_address" => $new_member->address ?? 0,
                            "billing_phone" => $new_member->contact_no ?? 0,
                            "billing_zip" => $new_member->zip_code ?? 0,
                            "shipping_name" => $new_member->name ?? 0,
                            "shipping_country" => $new_member->country ?? 0,
                            "shipping_state" => $new_member->state ?? 0,
                            "shipping_city" => $new_member->city ?? 0,
                            "shipping_address" => $new_member->address1 ?? 0,
                            "shipping_phone" => $new_member->contact_no ?? 0,
                            "shipping_zip" => $new_member->zip_code ?? 0,
                            "lang" => "en",
                            "balance" => 0.00,
                            "workspace" => $new_member->workspace_id ?? 0,
                            "created_at" => now(),
                            "updated_at" => now(),
                        ];

                        // insert customer
                        DB::table($customer_table)->insert($customer_data);
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