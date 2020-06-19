<?php

// unprotected
Route::group(['prefix' => 'provision'], function () {
    Route::get('/script', 'ProvisionController@script');
    Route::post('/register', 'ProvisionController@register');
});

Route::group(['prefix' => 'auth'], function () {
    Auth::routes([
        'register' => false,
        'verify' => false,
    ]);
});

// protected

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::apiResource('nodes', 'NodeController');
    Route::group(['prefix' => 'nodes'], function () {
        Route::post('/{node}/enable-netboot', 'NodeController@enableNetboot');
        Route::post('/{node}/disable-netboot', 'NodeController@disableNetboot');
    });
});



