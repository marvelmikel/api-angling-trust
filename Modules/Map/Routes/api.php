<?php

Route::group([
    'prefix' => 'map'
], function() {

    Route::get('stations', 'StationController@index');
    Route::get('markers', 'MarkerController@index');
    Route::get('stations/{station}', 'StationController@show');

});
