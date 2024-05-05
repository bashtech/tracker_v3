<?php

/**
 * Application endpoints.
 */
Route::get('/home', 'AppController@index')->name('home');
Route::get('/', 'AppController@index')->name('index');
Route::get('/impersonate-end/', 'ImpersonationController@endImpersonation')->name('end-impersonation');
Route::get('/impersonate/{user}', 'ImpersonationController@impersonate')->name('impersonate');

Route::get('/settings', 'UserSettingsController@show')->name('user.settings.show');
Route::patch('/settings', 'UserSettingsController@update')->name('user.settings.update');

Route::group(['prefix' => 'training'], function () {
    Route::get('sgt', 'TrainingController@sgtTraining')->name('training.sgt');
    Route::get('ssgt', 'TrainingController@index')->name('training.ssgt');
    Route::get('msgt', 'TrainingController@index')->name('training.msgt');
    Route::post('', 'TrainingController@update')->name('training.update');
});

Route::name('help.tickets.')->prefix('help/tickets')->group(function () {
    Route::get('', 'TicketController@index')->name('index');
    Route::get('setup', 'TicketController@setup')->name('setup');
    Route::get('create', 'TicketController@create')->name('create');
    Route::post('', 'TicketController@store')->name('store');
    Route::delete('', 'TicketController@store')->name('delete');
    Route::get('{ticket}', 'TicketController@show')->name('show');
    Route::patch('{ticket}/self-assign', 'TicketController@selfAssign')->name('self-assign');
    Route::patch('{ticket}/assign-to', 'TicketController@assignTo')->name('assign-to');
    Route::patch('{ticket}/place-on-hold', 'TicketController@placeOnHold')->name('place-on-hold');
    Route::patch('{ticket}/resume', 'TicketController@resume')->name('resume');
    Route::patch('{ticket}/resolve', 'TicketController@resolve')->name('resolve');
    Route::patch('{ticket}/reopen', 'TicketController@reopen')->name('reopen');
    Route::patch('{ticket}/reject', 'TicketController@reject')->name('reject');

    Route::name('comments.')->prefix('{ticket}/comments')->group(function () {
        Route::post('', 'TicketCommentController@store')->name('store');
        Route::delete('', 'TicketCommentController@delete')->name('delete');
    });
});

Route::get('developers', 'DeveloperController@index')->name('developer');
Route::post('developers/tokens', 'DeveloperController@generateToken')->name('developer.token.store');
Route::delete('developers/tokens', 'DeveloperController@destroyToken')->name('developer.token.delete');

/*
 * Application UI.
 */
Route::group(['prefix' => 'primary-nav'], function () {
    Route::get('collapse', function () {
        session(['primary_nav_collapsed' => true]);
    });
    Route::get('decollapse', function () {
        session(['primary_nav_collapsed' => false]);
    });
});

/*
 * Slack handler.
 */
Route::post('slack', [
    'as' => 'slack.commands',
    'uses' => 'Slack\SlackCommandController@index',
])->middleware('slack');
