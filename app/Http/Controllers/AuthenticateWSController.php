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
            $response[] = [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
            ];
        }
        
        return response()->json($response, 200);
    }

}
