<?php

return [
  "activated" => "2FA Activated",
  "button" => "Activate",
  "label" => "Time Based 2FA Code",
  "key_label" => "Key",
  "url_label" => "URL",
  "other_user_msg" => "A user must enable 2FA on their own account.",
  "get_app" => "Don't have a 2FA App? Get one for:",
  "enable" => [
    "description" => "Enhance the security of my account by requiring a time based 2FA code when logging in.",
    "button" => "Protect My Account with 2FA",
  ],
  "errors" =>[
    "code" => "The code was not accepted. Please try again.",
    "unknown" => "Something went wrong activating your 2FA code. Please try again.",
  ],
  "android" => [
    [
      "name" => "Google Authenticator",
      "url" => "https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2",
    ],
    [
      "name" => "LastPass Authenticator",
      "url" => "https://play.google.com/store/apps/details?id=com.lastpass.authenticator",
    ],
    [
      "name" => "Authy",
      "url" => "https://play.google.com/store/apps/details?id=com.authy.authy",
    ],
    [
      "name" => "Duo Mobile",
      "url" => "https://play.google.com/store/apps/details?id=com.duosecurity.duomobile",
    ],
    [
      "name" => "Microsoft Authenticator",
      "url" => "https://play.google.com/store/apps/details?id=com.azure.authenticator",
    ],
    [
      "name" => "Google Authenticator",
      "url" => "https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2",
    ]
  ],
  "ios" => [
    [
      "name" => "Google Authenticator",
      "url" => "https://apps.apple.com/us/app/google-authenticator/id388497605",
    ],
    [
      "name" => "LastPass Authenticator",
      "url" => "https://apps.apple.com/us/app/lastpass-authenticator/id1079110004",
    ],
    [
      "name" => "Authy",
      "url" => "https://apps.apple.com/us/app/authy/id494168017",
    ],
    [
      "name" => "Duo Mobile",
      "url" => "https://apps.apple.com/us/app/duo-mobile/id422663827",
    ],
    [
      "name" => "Microsoft Authenticator",
      "url" => "https://apps.apple.com/us/app/microsoft-authenticator/id983156458",
    ],
    [
      "name" => "Google Authenticator",
      "url" => "https://apps.apple.com/ca/app/google-authenticator/id388497605",
    ]
  ],
];
