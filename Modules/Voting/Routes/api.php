<?php

use Modules\Voting\Http\Controllers\RegistrationController;
use Modules\Voting\Http\Controllers\FormResponsesController;
use Modules\Voting\Http\Controllers\FormsController;

Route::group(['prefix' => 'voting', 'middleware' => ['auth.client']], function() {
    Route::post('forms', [FormsController::class, 'store']);
    Route::get('/forms/responses', [FormResponsesController::class, 'index']);
});

Route::group(['prefix' => 'voting', 'middleware' => ['auth.personal']], function() {
    Route::get('forms/mine', [FormsController::class, 'show']);
    Route::post('forms/mine/responses', [FormResponsesController::class, 'store']);
    Route::post('forms/mine/registration', [RegistrationController::class, 'store']);
});
