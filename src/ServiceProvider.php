<?php

namespace KindWork\TwoFa;

use Statamic\Providers\AddonServiceProvider as BaseProvider;

class ServiceProvider extends BaseProvider {
  protected $scripts = [
    __DIR__.'/../dist/js/scripts.js'
  ];
  
  protected $stylesheets = [
    __DIR__.'/../dist/css/styles.css'
  ];
  
  protected $routes = [
    'cp' => __DIR__.'/routes/cp.php'
  ];
  
  protected $middleware = [
    'cp' => [Middleware\CheckTwoFa::class],
  ];
  
  protected $fieldtypes = [
    Fieldtypes\TwoFaFieldtype::class
  ];
  
  public function boot() {
    parent::boot();
    $this->loadViewsFrom(__DIR__.'/resources/views', 'twofa');
    $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'twofa');
  }
}
