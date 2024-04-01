<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Dealer;
use Illuminate\Http\Request;

class DealersController extends Controller
{
    
    // to show all requests

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
    
    // to show accepted requests only

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
    
    // to show denied requests only

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
    
}
