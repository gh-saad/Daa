<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckContractStatus
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is logged in and contract status is pending
        if (auth()->check() && auth()->user()->{'contract-status'} === 'pending') {
            // Redirect to the dashboard if user tries to access any page other than "/dashboard"
            if ($request->path() !== 'dashboard') {
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
