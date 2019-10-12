<?php

namespace KindWork\TwoFa\Fieldtypes;

use Statamic\Facades\User;
use PragmaRX\Google2FAQRCode\Google2FA;
use PragmaRX\Google2FA\Support\Constants;

class TwoFaFieldtype extends \Statamic\Fields\Fieldtype {
  public function preload() {
    $email = User::current()->email();
    $google2fa = new Google2FA();
    $google2fa->setAlgorithm(Constants::SHA512);
    $google2fa->setKeyRegeneration(20);
    
    $secretKey = $google2fa->generateSecretKey(32);

    return [
      "actions" => [
        "activate" => cp_route("two-fa.activate"),
        "disable" => cp_route("two-fa.disable"),
      ],
      "deactivate" => __("twofa::deactivate"),
      "activate" => __("twofa::activate"),
      "activated" => __("twofa::activated"),
      "email" => $email,
      "key" => $secretKey,
      "url" => $google2fa->getQRCodeUrl(
        \Config::get("app.name"),
        $email,
        $secretKey
      ),
      "qrCode" => $google2fa->getQRCodeInline(
        \Config::get("app.name"),
        $email,
        $secretKey
      )
    ];
  }
}