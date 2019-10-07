<?php

namespace KindWork\TwoFa\Controllers;

use Statamic\Facades\User;
use Illuminate\Http\Request;
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
      $user = User::current();
      $user->set("two_fa", $key);
      $user->save();
    }
    
    return [
      "success" => $valid
    ];
  }
  
  public function authenticate(Request $request) {
    $this->boot();
    
    $secret = $request->input("code");
    $key = User::current()->data()["two_fa"];
    
    if ($key && $secret && $this->google2fa->verifyKey($key, $secret, $this->window)) {
      $request->session()->put("two_fa_authenticated", true);
      return redirect(cp_route("index"));
    } else {
      if (!$key) {
        $error = "2FA is not properly setup. Please set it up or contact your administrator for help.";
      } elseif (!$secret) {
        $error = "Please enter your code";
      } else {
        $error = "An unknown error occurred. Perhaps you made a mistake entering your code. Please try again.";
      }
      
      return view("twofa::2fa", ["error" => $error]);
    }
  }
}
