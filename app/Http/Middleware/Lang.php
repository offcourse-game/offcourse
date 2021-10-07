<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use App\models\GameSession;
use Closure;

class Lang
{
    /**
     * Handle an incoming request for localization.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Session::has('lang')){
            App::setlocale(Session::get('lang'));
        }elseif (is_numeric($request->route('sessionId'))){
            $lang = GameSession::getSessionOptionLanguage($request->route('sessionId'));
            App::setlocale($lang);
            Session::put('lang', $lang);
        }
        return $next($request);
    }
}
