<?php

namespace KindWork\TwoFa\Controllers;

use Statamic\Facades\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use PragmaRX\Google2FAQRCode\Google2FA;
use PragmaRX\Google2FA\Support\Constants;


class TwoFaController extends Controller {
  private $google2fa;
  private $window = 1;
  private $user;
  private $current_user;
  
  private function boot($user_id = null) {
    // Set up 2FA
    $this->google2fa = new Google2FA();
    $this->google2fa->setAlgorithm(Constants::SHA512);
    $this->google2fa->setKeyRegeneration(20);
    
    // Get current user for future reference
    $this->current_user = User::current();

    // Is there a user id param
    if ($user_id) {
      // Find the user
      $user = User::find($user_id);
      // Check to see if we have the user, and the user is current
      // or if the user can edit user passwords
      if ($user
        && ($user->id() == $this->current_user->id()
            || $this->current_user->can("users:edit-passwords"))) {
          $this->user = $user;
        }
    }
  }
  
  /**
   * Show the profile for the given user.
   *
   * @return View
   */
  public function index() {
    $this->current_user = User::current();
    
    // Check to see if the accout is locked, if so log out and show locked msg.    
    if ($this->current_user->data()["two_fa_locked"] ?? false) {
      Auth::logout();
      return view("twofa::locked");
    }
    
    return view("twofa::2fa");
  }
  
  /**
   * Show the profile for the given user.
   * @param  Request  $request
   * @return JSON (success or fail)
   */
  public function activate(Request $request) {
    $this->boot($request->input("id"));
    
    // Set up variables to check key & check if key is valid
    $secret = $request->input("secret");
    $key = $request->input("key");
    
    // Check that we have a user, key and code and the code and key match, if they do save the key to activate
    if ($this->user
      && $key
      && $secret
      && $valid = $this->google2fa->verifyKey($key, $secret, $this->window)) {
      $this->user->set("two_fa", $key);
      $this->user->save();
      $request->session()->put("two_fa_authenticated", true);
      $request->session()->pull("invalid_2fa_count");
    }
    
    // Return the success based on $valid
    return [
      "success" => $valid,
    ];
  }
  
  public function authenticate(Request $request) {
    $this->boot();

    // Check to see if the accout is locked, if so log out and show locked msg.    
    if ($this->current_user->data()["two_fa_locked"] ?? false) {
      Auth::logout();
      return view("twofa::locked");
    }
    
    // Set up variables
    $secret = $request->input("code");
    $key = $this->current_user->data()["two_fa"] ?? false;
    $invalid_2fa_count = $request->session()->get("invalid_2fa_count", 0);
    
    // Check code and if passes set auth on session and reset invalid count for session
    if ($key && $secret && $this->google2fa->verifyKey($key, $secret, $this->window)) {
      $request->session()->put("two_fa_authenticated", true);
      $request->session()->pull("invalid_2fa_count");
      return redirect(cp_route("index"));
    }

    // Set default error msg and then overide if key or secret missing.
    $error = __("twofa::errors.unknown");
    if (!$key) {
      $error = __("twofa::errors.setup");
    } elseif (!$secret) {
      $error = __("twofa::errors.code");
    }
    
    // If the invalid count is too high lock the account
    if ($invalid_2fa_count > 5) {
      $this->current_user->set("two_fa_locked", true);
      $this->current_user->save();
      Auth::logout();
      return view("twofa::locked");
    }

    // Increment the invalid count (we already returned way above if it was correct)
    $request->session()->put("invalid_2fa_count", ($invalid_2fa_count + 1));
    
    // Return the view with the error
    return view("twofa::2fa", ["error" => $error]);
  }
  
  public function disable(Request $request) {
    $this->boot($request->input("id"));
    
    // Set up variables & check if key is valid
    $secret = $request->input("secret");
    $key = $this->user->data()["two_fa"] ?? false;
    
    // Check that we hae a user, key ad code and the code and key match and if they are we can disable 2FA
    if ($this->user
      && $key
      && $secret
      && $valid = $this->google2fa->verifyKey($key, $secret, $this->window)) {
      $this->user->set("two_fa", null);
      $this->user->save();
      $request->session()->pull("two_fa_authenticated");
      $request->session()->pull("invalid_2fa_count");
    }
    
    // Return if we were successful disabling 2FA
    return [
      "success" => $valid,
    ];
  }
}
