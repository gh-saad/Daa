<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Events\NewUserRegistered;
use App\Events\NewUserWelcome;
use Illuminate\Http\Request;
use App\Models\Dealer;
use App\Models\User;

class ApprovalController extends Controller
{
    public function submit(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'contract' => 'required|mimes:pdf|max:10240', // Assuming PDF file with max size of 10MB
        ]);

        // Process the uploaded file
        if ($request->hasFile('contract')) {
            $file_name = time() . "_" . $request->contract->getClientOriginalName();
            $image = upload_file($request, 'contract', $file_name, 'documents');
            $uploadedFile = $request->file('contract');

            // add filename to dealers database
            $dealer = Dealer::where('user_id', $request->user_id)->first();
            $dealer->contract = 'uploads/documents/' . $file_name;
            $dealer->save();

            // send email to dealer that their contract has been submitted
            $user = User::findOrFail($request->user_id);
            event(new NewUserWelcome($user));

            // send email to admin that a new contract has been submitted and a new user has been registered
            event(new NewUserRegistered($user));

            // Redirect the user to a dashboard
            return redirect()->route('dashboard');
        }

        // If the file was not uploaded, redirect back with an error message
        return redirect()->back()->with('error_message', 'Failed to submit the contract. Please try again.');

    }

}
