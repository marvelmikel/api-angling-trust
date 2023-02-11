<?php

Route::group([
    'prefix' => 'fishing-draw',
    'namespace' => 'Any'
], function() {

    Route::post('{fishingDraw}/{fishingDrawPrize}', 'FishingDrawEntryController@store');

});

Route::group([
    'prefix' => 'fishing-draw',
    'namespace' => 'Client',
    'middleware' => ['auth.client']
], function() {

    Route::post('export', 'FishingDrawEntryController@export');

});
