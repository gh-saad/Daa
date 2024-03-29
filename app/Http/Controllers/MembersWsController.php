<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class MembersWsController extends Controller
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
                    "status" => "failed",
                ];
            }
        }
        return json_encode($result);
    }
}
