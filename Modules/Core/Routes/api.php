<?php

Route::group([
    'prefix' => 'client',
    'namespace' => 'Client',
    'middleware' => ['auth.client']
], function() {

    Route::get('options/{key}', 'OptionController@get');
    Route::post('options/{key}', 'OptionController@set');

});
