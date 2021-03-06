<?php

namespace App\Http\Middleware;

use Closure;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;

class AdminsAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if (auth()->user()?->name !== 'user'){
            session()->flash('error_message','Not allowed');
            return redirect()->back();
        }

        return $next($request);
    }
}
