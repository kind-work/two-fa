<?php

Route::get('two-fa', '\KindWork\TwoFa\Controllers\TwoFaController@index')->name('two-fa');
Route::post('two-fa', '\KindWork\TwoFa\Controllers\TwoFaController@authenticate')->name('two-fa.authenticate');
Route::post('two-fa/activate-two-fa', '\KindWork\TwoFa\Controllers\TwoFaController@activate')->name('two-fa.activate');
Route::post('two-fa/disable-two-fa', '\KindWork\TwoFa\Controllers\TwoFaController@disable')->name('two-fa.disable');
