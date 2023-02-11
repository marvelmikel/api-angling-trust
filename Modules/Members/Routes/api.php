<?php

Route::group([
    'prefix' => 'members',
    'namespace' => 'Personal',
    'middleware' => ['auth.personal']
], function() {

    Route::get('me', 'MemberController@me');
    Route::post('me', 'MemberController@updateMe');
    Route::post('me/preferences', 'MemberController@updateMyPreferences');
    Route::post('me/membership-type', 'MemberController@updateMyMembershipType');
    Route::post('me/chosen-payment-method', 'MemberController@updateMyChosenPaymentMethod');

    Route::post('me/renew', 'MemberRenewalController@renew');
    Route::post('me/complete', 'MemberRegistrationController@completeWithPayment');
    Route::post('me/make-a-payment', 'MemberPaymentController@makeAPayment');
    Route::post('me/make-an-offline-payment', 'MemberPaymentController@makeAnOfflinePayment');

    Route::post('me/update-map-permission', 'MemberController@updateMapPermission');

    Route::get('me/stripe-portal', 'MemberController@getStripePortalLink');

});

Route::group([
    'prefix' => 'membership_types',
    'namespace' => 'Client',
    'middleware' => ['auth.client']
], function() {

    Route::get('/', 'MembershipTypeController@index');
    Route::post('/', 'MembershipTypeController@update');
    Route::delete('{membershipType}', 'MembershipTypeController@destroy');
    Route::post('{membershipType}/restore', 'MembershipTypeController@restore');

});

Route::group([
    'prefix' => 'personal/membership_types',
], function() {

    Route::get('/', 'MembershipTypeController@index');
    Route::get('/{membershipTypeId}/categories', 'MembershipTypeController@categories');

});

Route::group([
    'prefix' => 'members',
    'namespace' => 'Client',
    'middleware' => ['auth.client']
], function() {

    Route::get('/', 'MemberController@index');
    Route::post('/', 'MemberController@store');
    Route::get('{member}', 'MemberController@show');
    Route::patch('{member}', 'MemberController@update');
    Route::delete('{member}', 'MemberController@destroy');
    Route::post('{member}/cancelRecurringPayment', 'MemberController@cancelRecurringPayment');
    Route::post('{member}/makeRecurringPayment', 'MemberController@makeRecurringPayment');
    Route::post('{member}/renew', 'MemberController@renew');
    Route::post('{member}/suspend', 'MemberController@suspend');
    Route::post('{member}/unsuspend', 'MemberController@unsuspend');
    Route::post('{member}/membershipType', 'MemberController@changeMembershipType');
    Route::post('{member}/markPackAsSent', 'MemberController@markPackAsSent');
    Route::post('{member}/completeRegistration', 'MemberController@completeRegistration');
    Route::post('{member}/at-fl-access', 'MemberController@updateATFLAccess');
    Route::post('{member}/updateExpiresAt', 'MemberController@updateExpiresAt');
    Route::post('{member}/activateLapsed', 'LapsedMemberController@activate');
    Route::post('{member}/addToPaymentHistory', 'MemberController@addToPaymentHistory');

});

Route::group([
    'prefix' => 'preferences',
    'namespace' => 'Any'
], function() {

    Route::get('/', 'PreferenceController@index');
    Route::get('disciplines', 'PreferenceController@disciplines');

});

Route::group([
    'prefix' => 'member_select_options',
    'namespace' => 'Any'
], function() {

    Route::get('/', 'MemberSelectOptionController@index');

});

Route::group([
    'prefix' => 'membership_type_categories',
    'namespace' => 'Client',
], function() {

    Route::get('/', 'MembershipTypeCategoryController@index');

});

Route::group([
    'prefix' => 'price_matrix',
    'namespace' => 'Any'
], function() {

    Route::get('club-or-syndicate', 'PriceMatrixController@clubOrSyndicate');

});

Route::group([
    'prefix' => 'stripe',
    'namespace' => 'Any'
], function() {

    Route::post('webhooks/payment_updated', 'StripeWebhookController@paymentUpdated');
    Route::post('webhooks/payment_failed', 'StripeWebhookController@paymentFailed');
    Route::post('webhooks/invoice_paid', 'StripeWebhookController@invoicePaid');

});
