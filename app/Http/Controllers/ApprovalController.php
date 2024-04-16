<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function submit(Request $request)
    {
        // // Validate the incoming request
        // $request->validate([
        //     'contract' => 'required|mimes:pdf|max:10240', // Assuming PDF file with max size of 10MB
        // ]);

        // // Process the uploaded file
        // if ($request->hasFile('contract')) {
        //     $uploadedFile = $request->file('contract');

        //     // Do something with the uploaded file, e.g., store it in the storage directory
        //     $filePath = $uploadedFile->store('contracts');

        //     // Update the contract status for the authenticated user
        //     $user = auth()->user();
        //     $user->contract_status = 'active';
        //     $user->save();

        //     // You can also send a notification email to the user informing them about the approval

        //     // Redirect the user to a success page or wherever you need
        //     return redirect()->route('success')->with('success_message', 'Your contract has been successfully submitted.');
        // }

        // // If the file was not uploaded, redirect back with an error message
        // return redirect()->back()->with('error_message', 'Failed to submit the contract. Please try again.');

        // dump all request data for now
        dd($request->all());
    }

}
