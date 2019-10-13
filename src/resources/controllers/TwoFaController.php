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
  
  private function boot() {
    $this->google2fa = new Google2FA();
    $this->google2fa->setAlgorithm(Constants::SHA512);
    $this->google2fa->setKeyRegeneration(20);
    $this->user = User::current();
  }
  
  /**
   * Show the profile for the given user.
   *
   * @return View
   */
  public function index() {
    return view("twofa::2fa");
  }
  
  /**
   * Show the profile for the given user.
   * @param  Request  $request
   * @return JSON (success or fail)
   */
  public function activate(Request $request) {
    $this->boot();
    
    // Set up variables to check key & check if key is valid
    $secret = $request->input("secret");
    $key = $request->input("key");
    
    // Check that the code and key match, if they do save the key to activate
    if ($key && $secret && $valid = $this->google2fa->verifyKey($key, $secret, $this->window)) {
      $this->user->set("two_fa", $key);
      $this->user->save();
    }
    
    // Return the success based on $valid
    return [
      "success" => $valid,
    ];
  }
  
  public function authenticate(Request $request) {
    $this->boot();

    // Check to see if the accout is locked, if so log out and show locked msg.    
    if ($this->user->data()["two_fa_locked"] ?? false) {
      Auth::logout();
      return view("twofa::locked");
    }
    
    // Set up variables
    $secret = $request->input("code");
    $key = $this->user->data()["two_fa"] ?? false;
    $invalid_2fa_count = $request->session()->get("invalid_2fa_count", 0);
    
    // Check code and if passes set auth on session and reset invalid count for session
    if ($key && $secret && $this->google2fa->verifyKey($key, $secret, $this->window)) {
      $request->session()->put("two_fa_authenticated", true);
      $request->session()->put("invalid_2fa_count", 0);
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
      $this->user->set("two_fa_locked", true);
      $this->user->save();
    }

    // Increment the invalid count (we already returned way above if it was correct)
    $request->session()->put("invalid_2fa_count", ($invalid_2fa_count + 1));
    
    // Return the view with the error
    return view("twofa::2fa", ["error" => $error]);
  }
  
  public function disable(Request $request) {
    $this->boot();
    
    // Set up variables & check if key is valid
    $secret = $request->input("secret");
    $key = $this->user->data()["two_fa"] ?? false;
    
    // Check that the code and key match and if they are we can disable 2FA
    if ($key && $secret && $valid = $this->google2fa->verifyKey($key, $secret, $this->window)) {
      $this->user->set("two_fa", null);
      $this->user->save();
    }
    
    // Return if we were successful disabling 2FA
    return [
      "success" => $valid,
    ];
  }
}
