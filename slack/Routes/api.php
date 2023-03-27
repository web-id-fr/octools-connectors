<?php

use Illuminate\Support\Facades\Route;
use Webid\OctoolsSlack\Http\Controllers\SlackController;

Route::group(config('octools.api_routes_group'), function () {
    Route::name('slack.')->prefix('slack')->group(function () {
        Route::get('/company-employees', [SlackController::class, 'getCompanyEmployees'])->name('company-employees');
        Route::post('/send-message-to-channel', [SlackController::class, 'sendMessageToChannel'])->name('send-message-to-channel');
        Route::get('/search-messages/{query}', [SlackController::class, 'searchMessages'])->name('search-messages');
    });
});

