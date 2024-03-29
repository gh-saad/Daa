<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthenticateWSController extends Controller
{

    // Generate a simple token
    public function generate_token($length = 32)
    {
        // Define characters that can be used in the token
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $token = '';

        // Generate random token
        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[rand(0, $charactersLength - 1)];
        }

        return $token;
    }

    // Authenticate users session and return token 
    public function authenticate(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        // Get user data from the database using provided credentials
        $user = User::where('email', $request->input('email'))->first();
        
        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        // Create a token for this user
        $token = $this->generate_token(32);
        $expiration = now()->addHours(3);

        // Store token in the database
        $user->update([
            'personal_access_token' => $token,
            'token_expires_at' => $expiration,
        ]);
        
        // Return success response with access token and user details
        return response()->json([
            'access_token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }

    // verify token and respond with all users data as a json array
    public function getmembers(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
        ]);
        
        // Check if user email is correct
        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        // Check if token matches the one stored in the database and if it hasn't expired yet
        if ($user->personal_access_token !== $request->input('token') || $user->token_expires_at < now()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        // Token is valid, respond with user data
        $users = User::all();
        
        // Construct JSON response
        $response = [];
        foreach ($users as $member) {
            // Split the name into first name and last name
            $name_parts = explode(' ', $member->name);
            $first_name = $name_parts[0]; // First part is the first name
            $last_name = implode(' ', array_slice($name_parts, 1)); // Join remaining parts as last name
            
            $response[] = [
                'member_id' => $member->member_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email_id' => $member->email,
                'company_name' => $member->company_name,
                'authentication_ref_id' => $member->authentication_ref_id,
                'contact_no' => $member->contact_no,
                'address' => $member->address,
                'address1' => $member->address1,
                'country' => $member->country,
                'state' => $member->state,
                'city' => $member->city,
                'gender' => $member->gender,
                'balance_privilege_point' => $member->balance_privilege_point,
                'member_type' => $member->type,
                'account_renewal_date' => $member->plan_expire_date,
            ];
        }

        // Return success response with all members data as an array
        return response()->json($response, 200);
    }

}
