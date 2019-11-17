<?php

namespace KindWork\TwoFa\Fieldtypes;

use Config;
use Spatie\Url\Url;
use Statamic\Facades\User;
use Illuminate\Http\Request;
use PragmaRX\Google2FAQRCode\Google2FA;
use PragmaRX\Google2FA\Support\Constants;

class TwoFaFieldtype extends \Statamic\Fields\Fieldtype {  
  private $url;
  private $name;
  private $email;
  private $id;
  private $secretKey;
  private $google2fa;
  private $error;
  
  public function __construct(Request $request) {
    $this->url = $request->url();
  }
  
  private function setup() {
    // Get the current url
    $url = Url::fromString($this->url);
    
    // Get the CP base url
    $cp_url = Url::fromString(cp_route("index"));
    
    // Set the intial offset to one
    $offset = 1;
    
    // While we have segments in the CP base url increment the offset
    while ($cp_url->getSegment($offset)) {
      $offset += 1;
    }

    // Get the current user for future use
    $current_user = User::current();

    // If the first section of the offset is users and we have a second
    // we assume that we are actually on a user edit profile page
    // also make sure that we can find the user and assign it to $user
    if ($url->getSegment($offset) == "users"
      && $url->getSegment($offset + 1)
      && $user = User::find($url->getSegment($offset + 1))) {
        
      // Check to see if the current user is equal to the selected user
      // or that the current user is able to edit user passwords
      if ($user->id() == $current_user->id()
        || $current_user->can("users:edit-passwords")) {

        // Get the app name
        $this->name = Config::get('app.name');

        // Store the user id & email
        $this->email = $user->email();
        $this->id = $user->id();

        // Set up 2FA
        $this->google2fa = new Google2FA();
        $this->google2fa->setAlgorithm(Constants::SHA512);
        $this->google2fa->setKeyRegeneration(20);
        $this->secretKey = $this->google2fa->generateSecretKey(32);
      } else {
        $this->error = __("twofa::activate.other_user_msg");
      }
    } else {
      // Set non user route error
      $this->error = __("twofa::errors.invalid_resource");
    }
  }
  
  public function preload() {
    $this->setup();

    // If the name was not defined then we are not on a user edit page, display an error
    if ($this->name == null) {
      return [
        "invalid_resource" => $this->error,
      ];
    }
    
    // Otherwise lets return the data needed for the Vue component
    return [
      "actions" => [
        "activate" => cp_route("two-fa.activate"),
        "disable" => cp_route("two-fa.disable"),
      ],
      "deactivate" => __("twofa::deactivate"),
      "activate" => __("twofa::activate"),
      "activated" => __("twofa::activated"),
      "email" => $this->email,
      "id" => $this->id,
      "key" => $this->secretKey,
      "url" => $this->google2fa->getQRCodeUrl(
        $this->name,
        $this->email,
        $this->secretKey
      ),
      "qrCode" => $this->google2fa->getQRCodeInline(
        $this->name,
        $this->email,
        $this->secretKey
      )
    ];
  }
}