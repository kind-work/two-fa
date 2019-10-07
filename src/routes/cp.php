<?php
  
Route::get('/two-fa', 'Controllers\TwoFaController@index')->name('two-fa');
Route::post('/two-fa', 'Controllers\TwoFaController@authenticate')->name('two-fa.authenticate');
Route::post('/two-fa/activate-two-fa', 'Controllers\TwoFaController@activate')->name('two-fa.activate');