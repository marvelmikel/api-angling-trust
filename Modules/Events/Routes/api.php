<?php

Route::group([
    'prefix' => 'events',
    'namespace' => 'Client',
    'middleware' => ['auth.client']
], function() {

    Route::get('/', 'EventController@index');
    Route::post('/', 'EventController@update');
    Route::delete('{event}', 'EventController@destroy');
    Route::post('{event}/restore', 'EventController@restore');
    Route::get('{event}/tickets', 'EventController@tickets');

    Route::post('{event}/purchase-export', 'PurchasedTicketController@export');

});

Route::group([
    'prefix' => 'purchasedTickets',
    'namespace' => 'Client',
    'middleware' => ['auth.client']
], function() {

    Route::get('{reference}', 'PurchasedTicketController@show');
    Route::post('{reference}/cancel', 'PurchasedTicketController@cancel');

});

Route::group([
    'prefix' => 'any',
    'namespace' => 'Any'
], function() {

    Route::get('events', 'EventController@index');
    Route::get('tickets/{ref}', 'TicketController@show');

    Route::get('events/checkout', 'CheckoutController@data');
    Route::post('events/checkout', 'CheckoutController@complete');
    Route::post('events/checkout/ticket/{purchasedTicket}/remove', 'CheckoutController@removeTicket');

    Route::post('tickets/{ref}/purchase', 'PurchasedTicketController@store');
    Route::post('ticket-purchase/{purchasedTicket}/complete', 'PurchasedTicketController@complete');
    Route::post('ticket-purchase/{purchasedTicket}/complete-free', 'PurchasedTicketController@completeFree');
    Route::post('ticket-purchase/{purchasedTicket}/cancel', 'PurchasedTicketController@cancel');

    Route::post('resend-tickets', 'PurchasedTicketController@resendTickets');
});
