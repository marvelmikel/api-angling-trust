<?php

Route::group([
    'prefix' => 'auth'
], function() {

    Route::group([
        'middleware' => ['auth.personal']
    ], function() {

        Route::get('me', 'UserController@me');
        Route::post('logout', 'SessionController@logout');

    });

    Route::group([
        'middleware' => ['guest.personal']
    ], function() {

        Route::post('login', 'SessionController@login');
        Route::post('register/step-1', 'SessionController@registerStep1');

    });

    Route::group([
        'middleware' => ['auth.personal']
    ], function() {

        Route::post('register/step-2', 'SessionController@registerStep2');
        Route::post('register/step-3', 'SessionController@registerStep3');
        Route::post('register/step-4', 'SessionController@registerStep4');

    });

    Route::group([
        'namespace' => 'Client',
        'middleware' => ['auth.client']
    ], function() {

        Route::post('{member}/loginAs', 'SessionController@loginAs');

    });

    Route::group([
        'prefix' => 'password-reset',
        'middleware' => ['guest.personal']
    ], function() {

        Route::post('generate', 'PasswordResetController@generate');
        Route::post('check', 'PasswordResetController@check');
        Route::post('complete', 'PasswordResetController@complete');

    });

});
