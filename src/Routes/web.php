<?php
Route::group(['middleware' => ['web']], function () {
    Route::get('/botauth', 'ZetRider\BotAuth\Http\Controllers\BotAuthController@index')->name('botauth.index');
    Route::post('/botauth', 'ZetRider\BotAuth\Http\Controllers\BotAuthController@check')->name('botauth.check');
    Route::post('/botauth/callback/{provider}', 'ZetRider\BotAuth\Http\Controllers\BotAuthController@callback');
    // For facebook confirm server...
    Route::get('/botauth/callback/{provider}', 'ZetRider\BotAuth\Http\Controllers\BotAuthController@callback');
});