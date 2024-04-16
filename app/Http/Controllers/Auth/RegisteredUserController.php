<?php

namespace App\Http\Controllers\Auth;

use App\Events\DefaultData;
use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Models\Role;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\WorkSpace;
use App\Events\GivePermissionToRole;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function __construct()
    {
        if(!file_exists(storage_path() . "/installed"))
        {
            header('location:install');
            die;
        }
        if(module_is_active('GoogleCaptcha') && admin_setting('google_recaptcha_is_on') == 'on' )
        {
            config(['captcha.secret' => admin_setting('google_recaptcha_secret')]);
            config(['captcha.sitekey' => admin_setting('google_recaptcha_key')]);
        }
        // $this->middleware('guest')->except('logout');
    }

    public function select($lang = '')
    {
        if (empty( admin_setting('signup')) ||  admin_setting('signup') == "on")
        {
            if($lang == '')
            {
                $lang = getActiveLanguage();
            }
            \App::setLocale($lang);
            return view('auth.type',compact('lang'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create_agent($lang = '')
    {
        if (empty( admin_setting('signup')) ||  admin_setting('signup') == "on")
        {
            if($lang == '')
            {
                $lang = getActiveLanguage();
            }
            \App::setLocale($lang);
            return view('auth.register',compact('lang'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create_agency($lang = '')
    {
        if (empty( admin_setting('signup')) ||  admin_setting('signup') == "on")
        {
            if($lang == '')
            {
                $lang = getActiveLanguage();
            }
            \App::setLocale($lang);

            // Fetch the available relationship managers from the User model
            $relationshipManagers = User::where('job_title', 'RM')->get();
    
            return view('auth.register_agency',compact('lang', 'relationshipManagers'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'store_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        if(module_is_active('GoogleCaptcha') && admin_setting('google_recaptcha_is_on') == 'on' )
        {
            $request->validate([
                'g-recaptcha-response' => 'required|captcha',
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        Auth::login($user);

        $role_r = Role::findByName('company');
        if(!empty($user))
        {
            $user->assignRole($role_r);
            // WorkSpace slug create on WorkSpace Model
            $workspace = new WorkSpace();
            $workspace->name = $request->store_name;
            $workspace->created_by = $user->id;
            $workspace->save();

            $user_work = User::find($user->id);
            $user_work->active_workspace = $workspace->id;
            $user_work->save();

            User::CompanySetting($user->id);
            $uArr = [
                'email'=> $request->email,
                'password'=> $request->password,
                'company_name'=>$request->name,
            ];
            $data= $user->MakeRole();
            // custom event for role
            $client_id =$data['client_role']->id;
            $staff_role =$data['staff_role']->id;

            if(!empty($user->active_module))
            {
                event(new GivePermissionToRole($client_id,'client',$user->active_module));
                event(new GivePermissionToRole($staff_role,'staff',$user->active_module));
                event(new DefaultData($user->id,$workspace->id,$user->active_module));
            }

            if(!empty($request->type) ? $request->type != "pricing" : '')
            {
                $plan = Plan::where('is_free_plan',1)->first();
                if($plan)
                {
                    $user->assignPlan($plan->id,'Month',$plan->modules,0,$user->id);
                }
            }

            if ( admin_setting('email_verification') == 'on')
            {
                try
                {
                    $admin_user = User::where('type','super admin')->first();
                    SetConfigEmail(!empty($admin_user->id) ? $admin_user->id : null);
                    $resp = EmailTemplate::sendEmailTemplate('New User', [$user->email], $uArr,$admin_user->id);
                    event(new Registered($user));
                }
                catch(\Exception $e)
                {
                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }
            }
            else
            {
                $user_work = User::find($user->id);
                $user_work->email_verified_at = date('Y-m-d h:i:s');
                $user_work->save();
            }

        }

        return redirect('plans');
        // return redirect(RouteServiceProvider::HOME);
    }

    public function store_agency(Request $request)
    {
        try 
        {
            // form validation rules here
            $request->validate([
                // required information
                'company_name' => 'required|string|max:255', // company name
                'email' => 'required|string|email|max:255|unique:users', // company email
                'password' => ['required', 'confirmed', Rules\Password::defaults()], // company password
                'relationship_manager' => 'required',  // relationship manager
                'company_whatsapp' => 'required', // company whatsapp
                // required documents
                'dealer_registration' => 'required|file|mimes:pdf,jpeg,png|max:2048', // dealer registration document
                'trade_license' => 'required|file|mimes:pdf,jpeg,png|max:2048', // trade license document
                'tax_registration' => 'required|file|mimes:pdf,jpeg,png|max:2048', //  tax registration document
                'passport_copy' => 'required|file|mimes:pdf,jpeg,png|max:2048', // passport copy image
                'emirate_document' => 'required|file|mimes:pdf,jpeg,png|max:2048', // emirates id or visa image
                'security_deposit_cheque_copy' => 'required|file|mimes:pdf,jpeg,png|max:2048', // security deposit cheque image
            ], [
                'email.unique' => "This Email is already registered.",
                'email.email' => "Please provide a valid Email.",
                'email.required' => "Email field can't be empty.",
                'password.required' => "The Password field can't be empty.",
                'password.confirmed' => "Confirmation does not match with the Password.",
                'relationship_manager.required' => "Please select a Relationship Manager.",
                'company_whatsapp.required' => "WhatsApp number is required.",
                'dealer_registration.required' => "Dealer Registration Document is required.",
                'dealer_registration.mimes' => "Only jpeg, png and pdf are allowed.",
                'trade_license.required' => "Trade License Document is required.",
                'trade_license.mimes' => "Only jpeg, png and pdf are allowed.",
                'tax_registration.required' => "Tax Registration Document is required.",
                'tax_registration.mimes' => "Only jpeg, png and pdf are allowed.",
                'passport_copy.required' => "Passport Copy Image is required.",
                'passport_copy.mimes' => "Only jpeg, png and pdf are allowed.",
                'emirate_document.required' => "Emirates ID/Visa Image is required.",
                'emirate_document.mimes' => "Only jpeg, png and pdf are allowed.",
                'security_deposit_cheque_copy.required' => "Security Deposit Cheque Image is required.",
                'security_deposit_cheque_copy.mimes' => "Only jpeg, png and pdf are allowed.",
            ]);
        
            if(module_is_active('GoogleCaptcha') && admin_setting('google_recaptcha_is_on') == 'on') {
                $request->validate([
                    'g-recaptcha-response' => 'required|captcha',
                ]);
            }
        
            // Save provided documents to storage and get the file paths
            $documentPaths = [];
            $inputs = ['dealer_registration', 'trade_license', 'tax_registration', 'passport_copy', 'emirate_document', 'security_deposit_cheque_copy'];
            foreach ($inputs as $input) {
                if ($request->hasFile($input)) {
                    $file_name = time() . "_" . $request->$input->getClientOriginalName();
                    $image = upload_file($request, $input, $file_name, 'documents');
                    $documentPaths[$input] = $file_name;
                }
            }

            // create a new user based on the request
            $user = User::create([
                'name' => $request->company_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->setAttribute('contract-status', 'pending'); // Set contract-status attribute
            Auth::login($user);
        
            $role_r = Role::findByName('company');
            if (!empty($user)) {
                $user->assignRole($role_r);
                $user->email_verified_at = now(); // mark this account as verified for now.
                $user->active_workspace = 1; // set current active workspace id
                $user->workspace_id = 1; // set default workspace id
                $user->active_plan = 1; // set free plan id to activate the user in free plan mode
                $user->plan_expire_date = now()->addDays(30); // set expiry date of free plan
                $user->is_enable_login = 1; // enable login for now will not be the case later
                $user->is_disable = 1;
                $user->created_by = 2; // default user
                $user->save();
            }
            
            // create a new dealer and assign it with the created user
            $dealer = Dealer::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'relational_manager' => $request->relationship_manager,
                'company_whatsapp' => $request->company_whatsapp,
                // add document paths to dealer model
                'dealer_document' => 'uploads/documents/' . $documentPaths['dealer_registration'],
                'trade_license' => 'uploads/documents/' . $documentPaths['trade_license'],
                'tax_document' => 'uploads/documents/' . $documentPaths['tax_registration'],
                'passport_copy' => 'uploads/documents/' . $documentPaths['passport_copy'],
                'emirates_document' => 'uploads/documents/' . $documentPaths['emirate_document'],
                'security_cheque_copy' => 'uploads/documents/' . $documentPaths['security_deposit_cheque_copy'],
                'created_by' => 2,
                'status' => 'pending',
            ]);

            return redirect('dashboard');
        } catch (ValidationException $e) {
            // Redirect back with errors if validation fails
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Handle any other exceptions, rollback transaction and display error message
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while processing your request. Please try again later.');
        }
    }
    
}
