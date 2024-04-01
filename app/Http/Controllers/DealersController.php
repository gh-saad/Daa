<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class DealersController extends Controller
{
    
    // to show all requests

    public function grid(Request $request)
    {
        if(Auth::user()->can('user manage'))
        {

            if(Auth::user()->type == 'super admin')
            {
                $users = User::where('type','company')->get();
            }
            else
            {
                if(Auth::user()->can('workspace manage'))
                {
                    $users = User::where('created_by',creatorId())->where('workspace_id',getActiveWorkSpace())->get();

                }
                else
                {

                    $users = User::where('created_by',creatorId())->get();
                }
            }
            return view('dealers.grid',compact('users'));
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
            if(Auth::user()->type == 'super admin')
            {
                $users = User::where('type','company')->get();
            }
            else
            {
                if(Auth::user()->can('workspace manage'))
                {
                    $users = User::where('created_by',creatorId())->where('workspace_id',getActiveWorkSpace())->get();
                }
                else
                {
                    $users = User::where('created_by',creatorId())->get();
                }
            }
            return view('dealers.list',compact('users'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    
    // to show accepted requests only

    public function accepted_grid(Request $request)
    {
        if(Auth::user()->can('user manage'))
        {

            if(Auth::user()->type == 'super admin')
            {
                $users = User::where('type','company')->get();
            }
            else
            {
                if(Auth::user()->can('workspace manage'))
                {
                    $users = User::where('created_by',creatorId())->where('workspace_id',getActiveWorkSpace())->get();

                }
                else
                {

                    $users = User::where('created_by',creatorId())->get();
                }
            }
            return view('dealers.accepted.grid',compact('users'));
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
            if(Auth::user()->type == 'super admin')
            {
                $users = User::where('type','company')->get();
            }
            else
            {
                if(Auth::user()->can('workspace manage'))
                {
                    $users = User::where('created_by',creatorId())->where('workspace_id',getActiveWorkSpace())->get();
                }
                else
                {
                    $users = User::where('created_by',creatorId())->get();
                }
            }
            return view('dealers.accepted.list',compact('users'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    
    // to show denied requests only

    public function denied_grid(Request $request)
    {
        if(Auth::user()->can('user manage'))
        {

            if(Auth::user()->type == 'super admin')
            {
                $users = User::where('type','company')->get();
            }
            else
            {
                if(Auth::user()->can('workspace manage'))
                {
                    $users = User::where('created_by',creatorId())->where('workspace_id',getActiveWorkSpace())->get();

                }
                else
                {

                    $users = User::where('created_by',creatorId())->get();
                }
            }
            return view('dealers.denied.grid',compact('users'));
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
            if(Auth::user()->type == 'super admin')
            {
                $users = User::where('type','company')->get();
            }
            else
            {
                if(Auth::user()->can('workspace manage'))
                {
                    $users = User::where('created_by',creatorId())->where('workspace_id',getActiveWorkSpace())->get();
                }
                else
                {
                    $users = User::where('created_by',creatorId())->get();
                }
            }
            return view('dealers.denied.list',compact('users'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    
}
