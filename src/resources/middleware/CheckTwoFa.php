<?php

namespace KindWork\TwoFa\Middleware;

use Closure;
use Illuminate\Http\Request;
use Statamic\Facades\User;

class CheckTwoFa {   
  public function handle(Request $request, Closure $next) {
    $user = User::current();

    if (
        // If we are on the 2FA auth route
        $request->path() == "cp/two-fa" &&
        // Check to see if we already authed with 2FA on the session
        $request->session()->get("two_fa_authenticated")
    ) {
      // Go to the CP Index
      return redirect(cp_route("index"));    
    }
    
    if (
        // make sure we have a user
        $user &&
        // make sure two_fa is set
        isset($user->data()["two_fa"]) &&
        // make sure we have a key
        !empty($user->data()["two_fa"]) &&
        // make sure we are not on the 2FA auth route
        $request->path() !== "cp/two-fa" &&
        // make sure we are not already authed with 2FA on the session
        !$request->session()->get("two_fa_authenticated")
    ) {
      // If we get here go to the two FA page to get the code
      return redirect(cp_route("two-fa"));
    }

    // Otherwise lets continue
    return $next($request);
  }
}