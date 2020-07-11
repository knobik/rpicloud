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
    // config
    Route::get('/config', 'ConfigController@index');

    // nodes
    Route::apiResource('nodes', 'NodeController');
    Route::group(['prefix' => 'nodes'], function () {
        Route::post('/{node}/enable-netboot', 'NodeController@enableNetboot');
        Route::post('/{node}/disable-netboot', 'NodeController@disableNetboot');
        Route::post('/{node}/reboot', 'NodeController@reboot');
        Route::post('/{node}/shutdown', 'NodeController@shutdown');

        // node operations
        Route::get('/{node}/operations', 'OperationController@index');
    });
});



