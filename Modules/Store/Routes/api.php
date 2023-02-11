<?php

use Modules\Store\Http\Controllers\Any\DonationController;
use Modules\Store\Http\Controllers\Personal\StripeController;

Route::group([
    'prefix' => 'payment_methods/stripe',
    'namespace' => 'Personal',
    'middleware' => ['auth.personal']
], function() {

    Route::get('/', 'StripeController@index');
    Route::post('/', 'StripeController@store');
    Route::get('intent', 'StripeController@intent');
    Route::delete('{id}', 'StripeController@destroy');

    Route::post('subscribe', 'StripeController@subscribe');
    Route::post('complete', [StripeController::class, 'completePayment'])
        ->name('stripe.complete-payment');
    Route::post('record', 'StripeController@recordPayments');
    Route::post('record-other', 'StripeController@recordOther');
    // Route::get('donations-export', 'DonationController@export');

});

Route::group([
    'prefix' => 'payment_methods/smart_debit',
    'namespace' => 'Personal',
    'middleware' => ['auth.personal']
], function() {

    Route::post('validate', 'SmartDebitController@validateDetails');
    Route::post('donation', 'SmartDebitController@donation');
    Route::post('membership', 'SmartDebitController@membership');
    Route::post('renewMembership', 'SmartDebitController@renewMembership');

});

Route::group([
    'prefix' => 'payment_methods/offline',
    'namespace' => 'Personal',
    'middleware' => ['auth.personal']
], function() {

    Route::post('/', 'OfflinePaymentController@makeOfflinePayment');

});

Route::group([
    'prefix' => 'cart',
    'middleware' => ['auth.personal']
], function() {

    Route::get('/', 'CartController@get');

});

Route::group([
    'prefix' => 'any',
    'namespace' => 'Any'
], function() {

    Route::get('payment/intent', 'PaymentController@intent');
    Route::post('donation', 'DonationController@store');
  
    
   


});

Route::group([
    'prefix' => 'member_payment_history',
    'namespace' => 'Client',
    'middleware' => ['auth.client']
], function() {
    Route::get('{member}', 'PaymentController@index');
});

Route::get('/donations', [DonationController::class, 'index']);

Route::get('donations-export', [DonationController::class, 'export']);






