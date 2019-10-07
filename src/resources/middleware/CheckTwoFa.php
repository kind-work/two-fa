<?php

namespace KindWork\TwoFa\Middleware;

use Log;
use Closure;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Statamic\Facades\User;
use Statamic\Exceptions\AuthenticationException;
use Statamic\Exceptions\AuthorizationException;

class CheckTwoFa
{   
    public function handle(Request $request, Closure $next)
    {
//         $google2fa = new Google2FA();
      
        $user = User::current();
        
//         dump($google2fa->generateSecretKey());
        
        if ($user && isset($user->toArray()['two_fa']) && $request->path() !== 'cp/two-fa' && !$request->session()->get('two_fa_authenticated')) {
            return redirect('/cp/two-fa');
        }

        return $next($request);
    }
}