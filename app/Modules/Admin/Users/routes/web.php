<?php

Route::group(['prefix' => 'admin', 'middleware' => [], 'namespace' => 'App\Modules\Admin\Users\Controllers', 'as' => 'admin.'], function () {
    Route::resource('user', 'UsersController');
});
