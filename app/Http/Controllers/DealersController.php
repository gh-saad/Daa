<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DealersController extends Controller
{
    // to show all dealers

    public function grid(Request $request)
    {
        if(Auth::user()->can('user manage'))
        {
            $dealers = Dealer::all()->sortBy('id'); 
            return view('dealers.grid',compact('dealers'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function list(Request $request) 
    {
        if(Auth::user()->can('user manage'))
        {
            $dealers = Dealer::all()->sortBy('id'); 
            return view('dealers.list',compact('dealers'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    
    // to show accepted dealers only

    public function accepted_grid(Request $request)
    {
        if(Auth::user()->can('user manage'))
        {
            $dealers = Dealer::where('status','Approved')->get(); 
            return view('dealers.accepted.grid',compact('dealers'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function accepted_list(Request $request) 
    {
        if(Auth::user()->can('user manage'))
        {
            $dealers = Dealer::where('status','Approved')->get(); 
            return view('dealers.accepted.list',compact('dealers'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    
    // to show denied dealers only

    public function denied_grid(Request $request)
    {
        if(Auth::user()->can('user manage'))
        {
            $dealers = Dealer::where('status','Rejected')->get(); 
            return view('dealers.denied.grid',compact('dealers'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function denied_list(Request $request) 
    {
        if(Auth::user()->can('user manage'))
        {
            $dealers = Dealer::where('status','Rejected')->get(); 
            return view('dealers.denied.list',compact('dealers'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    
    // to edit a dealer

    public function edit($id)
    {
        if(Auth::user()->can('user edit'))
        {
            $dealer = Dealer::find($id);
            $relationshipManagers = User::where('job_title', 'RM')->get();
            return view('dealers.edit',compact('dealer','relationshipManagers'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // to update a dealer

    public function update(Request $request, $id)
    {
        if(Auth::user()->can('user edit'))
        {
            try 
            {
                // form validation rules here
                $request->validate([
                    // validating information
                    'dealer_name' => 'required|string|max:255', // dealer name
                    'dealer_website' => 'nullable|url', // website url
                    'dealer_whatsapp' => 'required', // whatsapp number
                    'po_box' => 'nullable', // po box
                    'gm_whatsapp' => 'nullable', // general manager whatsapp number
                    'marketing_director_no' => 'nullable', // marketing director phone number
                    'relationship_manager' => 'required', // relationship manager id
                    'currency' => 'nullable', // currency code
                    'bank_name' => 'nullable|string|max:255', // bank name
                    'account_name' => 'nullable|string|max:255', // account name
                    'bank_address' => 'nullable', // bank address
                    'iban' => 'nullable', // iban
                    'swift_code' => 'nullable', // swift code
                    'branch_name' => 'nullable|string|max:255', // branch name
                    'dealer_logo' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048', // image only file
                    'dealer_balance' => 'nullable', // dealer balance
                    // validating documents
                    'dealer_document' =>  'file|mimes:pdf,png,jpg|max:3072', // dealer document file
                    'passport_copy' =>  'file|mimes:pdf,png,jpg|max:3072', // passport copy file
                    'trade_license_document' => 'file|mimes:pdf,png,jpg|max:3072', // trade license document file
                    'emirates_document' => 'file|mimes:pdf,png,jpg|max:3072', // emirates document file
                    'tax_document' => 'file|mimes:pdf,png,jpg|max:3072', // tax document file
                    'security_deposit_cheque_copy' => 'file|mimes:pdf,png,jpg|max:3072', // security deposit cheque copy file
                    'contract' => 'file|mimes:pdf,png,jpg|max:3072', // contract of agreement
                ], [
                    'dealer_name.required' => "Dealer Name is required.",
                    'dealer_website.url' => "Invalid URL format.",
                    'dealer_whatsapp.required' => "WhatsApp Number is required.",
                    'relationship_manager.required' => "Relationship Manager is required.",
                    'dealer_logo.mimes' => "Invalid image file.",
                    'dealer_logo.max' => "Image size should be less than 2MB.",
                    'dealer_document.required' => "Dealer Registration Document is required.",
                    'dealer_document.mimes' => "Only JPG/PDF/PNG files are allowed.",
                    'dealer_document.max' => "File size should not exceed 3MB.",
                    'passport_copy.required' => "Passport Copy is required.",
                    'passport_copy.mimes' => "Only JPG/PDF/PNG files are allowed.",
                    'passport_copy.max' => "File size should not exceed 3MB.",
                    'trade_license_document.required' => "Trade License Document is required.",
                    'trade_license_document.mimes' => "Only JPG/PDF/PNG files are allowed.",
                    'trade_license_document.max' => "File size should not exceed 3MB.",
                    'emirates_document.required' => "Emirates ID is required.",
                    'emirates_document.mimes' => "Only JPG/PDF/PNG files are allowed.",
                    'emirates_document.max' => "File size should not exceed 3MB.",
                    'tax_document.required' => "Tax Document is required.",
                    'tax_document.mimes' => "Only JPG/PDF/PNG files are allowed.",
                    'tax_document.max' => "File size should not exceed 3MB.",
                    'security_deposit_cheque_copy.required' => "Security Deposit Cheque Image is required.",
                    'security_deposit_cheque_copy.mimes' => "Only JPG/PDF/PNG files are allowed.",
                    'security_deposit_cheque_copy.max' => "File size should not exceed 3MB.",
                    'contract.required' => "Contract is required.",
                    'contract.mimes' => "Only JPG/PDF/PNG files are allowed.",
                    'contract.max' => "File size should not exceed 3MB.",
                ]);

                // save provided documents to storage and get the file paths
                $documentPaths = [];
                $inputs = ['dealer_document', 'passport_copy', 'trade_license_document', 'emirates_document', 'tax_document', 'security_deposit_cheque_copy', 'contract'];
                foreach ($inputs as $input) {
                    if ($request->hasFile($input)) {
                        $file = $request->file($input);
                        $file_name = time() . '_' . $file->getClientOriginalName();
                        $file->storeAs('documents/', $file_name);

                        // Get the file URL do nothing with it for now
                        $url = Storage::url('documents/' . $file_name);

                        // Store the URL in the documentPaths array
                        $documentPaths[$input] = $file_name;
                    }else{
                        // Have all inputs as empty
                        $documentPaths[$input] = '';
                    }
                }

                // save the provided logo image to storage and get the file path
                if ($request->hasFile('dealer_logo')){
                    $logo_file = $request->file('dealer_logo');
                    $logo_file_name = time() . "_" . $logo_file->getClientOriginalName();
                    $logo_file->storeAs('dealer-logos/', $logo_file_name);

                    // Get the file URL do nothing with it for now
                    $url = Storage::url('dealer-logos/' . $logo_file_name);
                }

                // update the data for this dealer in the database
                $dealer = Dealer::find($id);
                $dealer->company_name = $request->dealer_name;
                $dealer->website = $request->dealer_website;
                $dealer->company_whatsapp = $request->dealer_whatsapp;
                $dealer->po_box = $request->po_box;
                $dealer->GM_whatsapp = $request->gm_whatsapp;
                $dealer->marketing_director_no = $request->marketing_director_no;
                $dealer->relational_manager = $request->relationship_manager;
                $dealer->currency = $request->currency;
                $dealer->bank_name = $request->bank_name;
                $dealer->ac_name = $request->account_name;
                $dealer->branch_address = $request->bank_address;
                $dealer->iban = $request->iban;
                $dealer->swift_code = $request->swift_code;
                $dealer->branch_name = $request->branch_name;
                // Check if files were provided for documents
                if ($request->hasFile('dealer_document')) {
                    $dealer->dealer_document = 'uploads/documents/' . $documentPaths['dealer_document'];
                }
                if ($request->hasFile('passport_copy')) {
                    $dealer->passport_copy = 'uploads/documents/' . $documentPaths['passport_copy'];
                }
                if ($request->hasFile('trade_license_document')) {
                    $dealer->trade_license = 'uploads/documents/' . $documentPaths['trade_license_document'];
                }
                if ($request->hasFile('emirates_document')) {
                    $dealer->emirates_document = 'uploads/documents/' . $documentPaths['emirates_document'];
                }
                if ($request->hasFile('tax_document')) {
                    $dealer->tax_document = 'uploads/documents/' . $documentPaths['tax_document'];
                }
                if ($request->hasFile('security_deposit_cheque_copy')) {
                    $dealer->security_cheque_copy = 'uploads/documents/' . $documentPaths['security_deposit_cheque_copy'];
                }
                if ($request->hasFile('contract')) {
                    $dealer->contract = 'uploads/documents/' . $documentPaths['contract'];
                }
                
                // Check if logo file was provided
                if ($request->hasFile('dealer_logo')) {
                    $dealer->logo = 'uploads/dealer-logos/' . $logo_file_name;
                }
                
                $dealer->save();

                // update dealer balance field in the user database
                $user = User::find($dealer->user_id);
                $user->{'balance-amount'} = $request->dealer_balance;
                $user->save();

                if ($request->status == 'pending'){
                    // do nothing
                }else if ($request->status == 'approve'){
                    // update status for both dealer and users contract status
                    $user->{'contract-status'} = 'active';
                    $user->save();

                    $dealer->status = 'Approved';
                    $dealer->save();
                }else if ($request->status == 'reject'){
                    // update status for both dealer and users contract status
                    $user->{'contract-status'} = 'inactive';
                    $user->save();

                    $dealer->status = 'Rejected';
                    $dealer->save();
                }

                // redirect back with success
                return redirect()->back()->with('success', 'Dealer updated Successfully!');

            } catch (ValidationException $e)  {
                // redirect back with errors if validation fails
                return redirect()->back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                // handle any other exceptions, rollback transaction and display error message
                DB::rollBack();
                return redirect()->back()->with('error', 'An error occurred while processing your request. Please try again later.');
            }
        }
        else
        {
            // redirect back with error if user is not authorized
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    
    // to view a dealer

    public function view($id)
    {
        if(Auth::user()->can('user edit'))
        {
            $dealer = Dealer::find($id);
            $roles = Role::where('created_by',\Auth::user()->id)->pluck('name','id');
            return view('dealers.view',compact('dealer','roles'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    
    // to delete a dealer

    public function destroy($id)
    {
        if(Auth::user()->can('user delete'))
        {
            $dealer = Dealer::findOrFail($id);
            
            try{
                \DB::beginTransaction();
                
                $dealer->delete();

                \DB::commit();
            }catch(\Exception $e){
                \DB::rollBack();
                return redirect()->back()->with('error', __($e->getMessage()));
            }
            return redirect()->route('backend.dealers.grid')->with('success', __('Dealer successfully deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // to create a dealer

    public function create()
    {
        if(Auth::user()->can('user create'))
        {
            $users = User::all()->sortBy('id'); 
            $relationshipManagers = User::where('job_title', 'RM')->get();
            return view('dealers.create',compact('users', 'relationshipManagers'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    // to store a dealer

    public function store(Request $request)
    {
        if(Auth::user()->can('user create'))
        {
            try 
            {
                // form validation rules here
                $request->validate([
                    // required information
                    'user_name' => 'required|string|max:255', // user name
                    'email' => 'required|string|email|max:255|unique:users', // user email
                    'password' => 'required|confirmed|min:8', // user password
                    'workspace_name' => 'required|string|max:25', // workspace name
                    'dealer_name' => 'required|string|max:255', // dealer name
                    'dealer_whatsapp' => 'required', // whatsapp number
                    'relationship_manager' => 'required', // relationship manager id
                    // required documents
                    'dealer_document' =>  'file|mimes:pdf,png,jpg|max:3072', // dealer document file
                    'passport_copy' =>  'file|mimes:pdf,png,jpg|max:3072', // passport copy file
                    'trade_license_document' => 'file|mimes:pdf,png,jpg|max:3072', // trade license document file
                    'emirates_document' => 'file|mimes:pdf,png,jpg|max:3072', // emirates document file
                    'tax_document' => 'file|mimes:pdf,png,jpg|max:3072', // tax document file
                    'security_deposit_cheque_copy' => 'file|mimes:pdf,png,jpg|max:3072', // security deposit cheque copy file
                    'contract' => 'file|mimes:pdf,png,jpg|max:3072', // contract of agreement
                ], [
                    'user_name.required' => "User name is required.",
                    'email.unique' => "This Email is already registered.",
                    'email.email' => "Please provide a valid Email.",
                    'email.required' => "Email field can't be empty.",
                    'password.required' => "The Password field can't be empty.",
                    'password.confirmed' => "Confirmation does not match with the Password.",
                    'workspace_name' => "Workspace Name is required.",
                    'dealer_name.required' => "Dealer Name is required.",
                    'dealer_whatsapp.required' => "WhatsApp Number is required.",
                    'relationship_manager.required' => "Relationship Manager is required.",
                    'dealer_document.required' => "Dealer Registration Document is required.",
                    'dealer_document.mimes' => "Only JPG/PDF/PNG files are allowed.",
                    'dealer_document.max' => "File size should not exceed 3MB.",
                    'passport_copy.required' => "Passport Copy is required.",
                    'passport_copy.mimes' => "Only JPG/PDF/PNG files are allowed.",
                    'passport_copy.max' => "File size should not exceed 3MB.",
                    'trade_license_document.required' => "Trade License Document is required.",
                    'trade_license_document.mimes' => "Only JPG/PDF/PNG files are allowed.",
                    'trade_license_document.max' => "File size should not exceed 3MB.",
                    'emirates_document.required' => "Emirates ID is required.",
                    'emirates_document.mimes' => "Only JPG/PDF/PNG files are allowed.",
                    'emirates_document.max' => "File size should not exceed 3MB.",
                    'tax_document.required' => "Tax Document is required.",
                    'tax_document.mimes' => "Only JPG/PDF/PNG files are allowed.",
                    'tax_document.max' => "File size should not exceed 3MB.",
                    'security_deposit_cheque_copy.required' => "Security Deposit Cheque Image is required.",
                    'security_deposit_cheque_copy.mimes' => "Only JPG/PDF/PNG files are allowed.",
                    'security_deposit_cheque_copy.max' => "File size should not exceed 3MB.",
                    'contract.required' => "Contract is required.",
                    'contract.mimes' => "Only JPG/PDF/PNG files are allowed.",
                    'contract.max' => "File size should not exceed 3MB.",
                ]);

                // save provided documents to storage and get the file paths
                $documentPaths = [];
                $inputs = ['dealer_document', 'passport_copy', 'trade_license_document', 'emirates_document', 'tax_document', 'security_deposit_cheque_copy', 'contract'];
                foreach ($inputs as $input) {
                    if ($request->hasFile($input)) {
                        $file = $request->file($input);
                        $file_name = time() . '_' . $file->getClientOriginalName();
                        $file->storeAs('documents/', $file_name);

                        // Get the file URL do nothing with it for now
                        $url = Storage::url('documents/' . $file_name);

                        // Store the URL in the documentPaths array
                        $documentPaths[$input] = $file_name;
                    }
                }

                // save the provided logo image to storage and get the file path
                if ($request->hasFile('dealer_logo')){
                    $logo_file = $request->file('dealer_logo');
                    $logo_file_name = time()  . "_" . $logo_file->getClientOriginalName();
                    $logo_file->storeAs('dealer-logos/', $logo_file_name);

                    // Get the file URL do nothing with it for now
                    $url = Storage::url('dealer-logos/' . $logo_file_name);
                }

                // add a new user in the database using the provided data
                $user  = User::create([
                    'name' => $request->user_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'company_name' => $request->dealer_name,
                    'country' => $request->country,
                    'contact_no' => $request->phone,
                    'address' => $request->address,
                    'avatar' => 'uploads/dealer-logos/' . $logo_file_name,
                ]);

                $role_r = Role::findByName('company');
                if (!empty($user)) {
                    $user->assignRole($role_r);
                    $user->email_verified_at = now(); // mark this account as verified for now.
                    $user->{'contract-status'} = 'active'; // set contract status to active
                    $user->active_workspace = 1; // set current active workspace id
                    $user->workspace_id = 1; // set default workspace id
                    $user->active_plan = 1; // set free plan id to activate the user in free plan mode
                    $user->plan_expire_date = now()->addDays(30); // set expiry date of free plan
                    $user->is_enable_login = 0; // disable login for now will not be the case later
                    $user->is_disable = 1;
                    $user->created_by = 2; // default user
                    $user->save();
                }

                // add a new dealer in the database using the provided data
                $dealer = Dealer::create([
                    'user_id' => $user->id,
                    'company_name' => $request->dealer_name,
                    'website' => $request->dealer_website,
                    'relational_manager' => $request->relationship_manager,
                    'company_whatsapp' => $request->dealer_whatsapp,
                    'bank_name' => $request->bank_name,
                    'ac_name' => $request->account_holder_name,
                    'branch_name' => $request->branch_name,
                    'branch_address' => $request->bank_address,
                    'iban' => $request->account_number,
                    'swift_code' => $request->swift_code,
                    'logo' => 'uploads/dealer-logos/' . $logo_file_name,
                    'po_box' => $request->p_o_box,
                    // add document paths to dealer model
                    'dealer_document' => 'uploads/documents/' . $documentPaths['dealer_document'],
                    'trade_license' => 'uploads/documents/' . $documentPaths['trade_license_document'],
                    'tax_document' => 'uploads/documents/' . $documentPaths['tax_document'],
                    'passport_copy' => 'uploads/documents/' . $documentPaths['passport_copy'],
                    'emirates_document' => 'uploads/documents/' . $documentPaths['emirates_document'],
                    'security_cheque_copy' => 'uploads/documents/' . $documentPaths['security_deposit_cheque_copy'],
                    'contract' => 'uploads/documents/' . $documentPaths['contract'],
                    'created_by' => 2,
                    'status' => 'pending',
                ]);

                // redirect back with success
                return redirect()->back()->with('success', 'Dealer created Successfully!');

            } catch (ValidationException $e)  {
                // redirect back with errors if validation fails
                return redirect()->back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                // handle any other exceptions, rollback transaction and display error message
                DB::rollBack();
                return redirect()->back()->with('error', 'An error occurred while processing your request. Please try again later.');
            }
        }
        else
        {
            // redirect back with error if user is not authorized
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

}
