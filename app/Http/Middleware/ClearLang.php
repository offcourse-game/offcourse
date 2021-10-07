<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Session;
use Closure;

class ClearLang
{
    /**
     * Handle an incoming request for localization.
     * This deletes an existing lang setting.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Session::forget('lang');
        return $next($request);
    }
}
