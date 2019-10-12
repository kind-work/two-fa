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
       
    $secret = $request->input("secret");
    $key = $request->input("key");
    
    if ($valid = $this->google2fa->verifyKey($key, $secret, $this->window)) {
      $this->user->set("two_fa", $key);
      $this->user->save();
    }
    
    return [
      "success" => $valid,
    ];
  }
  
  public function authenticate(Request $request) {
    $this->boot();
    
    $secret = $request->input("code");
    $key = $this->user->data()["two_fa"];
    $two_fa_locked = $this->user->data()["two_fa_locked"] ?? false;
    $invalid_2fa_count = $request->session()->get("invalid_2fa_count", 0);
    
    if ($two_fa_locked) {
      Auth::logout();
      return view("twofa::locked", ["error" => "Your account has been locked. Please contact an administrator to unlock your account."]);
    }
    
    if ($key && $secret && $this->google2fa->verifyKey($key, $secret, $this->window)) {
      $request->session()->put("two_fa_authenticated", true);
      $request->session()->put("invalid_2fa_count", 0);
      return redirect(cp_route("index"));
    }

    $error = "An unknown error occurred. Perhaps you made a mistake entering your code. Please try again.";

    if (!$key) {
      $error = "2FA is not properly setup. Please set it up or contact your administrator for help.";
    } elseif (!$secret) {
      $error = "Please enter your code";
    }
    
    if ($invalid_2fa_count > 5) {
      $this->user->set("two_fa_locked", true);
      $this->user->save();
    }

    $request->session()->put("invalid_2fa_count", ($invalid_2fa_count + 1));
    
    return view("twofa::2fa", ["error" => $error]);
  }
  
  public function disable(Request $request) {
    $this->boot();
    
    $secret = $request->input("secret");
    $key = $this->user->data()["two_fa"];
    $valid = $this->google2fa->verifyKey($key, $secret, $this->window);
    
    if ($key && $secret && $valid) {
      $this->user->set("two_fa", null);
      $this->user->save();
    }
    
    return [
      "success" => $valid,
    ];
  }
}
