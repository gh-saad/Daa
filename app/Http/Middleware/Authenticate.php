<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\FetchCurrenciesService;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate extends Middleware
{
    protected $fetchCurrenciesService;
    protected $auth;

    public function __construct(Auth $auth, FetchCurrenciesService $fetchCurrenciesService)
    {
        parent::__construct($auth); // Ensure parent constructor is called
        $this->auth = $auth;
        $this->fetchCurrenciesService = $fetchCurrenciesService;
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        if ($this->auth->check()) {
            $lastFetched = DB::table('currency')->value('last_fetched');

            if (!$lastFetched || Carbon::parse($lastFetched)->addDay()->isPast()) {
                $this->fetchCurrenciesService->fetchAndUpdateCurrencies();
            }
        }

        return $next($request);
    }
}
