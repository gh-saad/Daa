<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Dealer;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

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
            $roles = Role::where('created_by',\Auth::user()->id)->pluck('name','id');
            return view('dealers.edit',compact('dealer','roles'));
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
            //
        }
        else
        {
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
            $roles = Role::where('created_by',\Auth::user()->id)->pluck('name','id');
            return view('dealers.create',compact('roles'));
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
            //
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

}
