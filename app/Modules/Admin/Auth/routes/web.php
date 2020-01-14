<?php

Route::group( ['prefix' => 'admin', 'namespace' => 'App\Modules\Admin\Auth\Controllers','as' => 'admin.', 'middleware' => 'web'], function(){

    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login')->name('singIn');
    Route::post('logout', 'LoginController@logout')->name('logout');
    // Registration Routes...
    Route::get('register', 'ConfirmPasswordController@showConfirmForm')->name('register');
    Route::post('register', 'ConfirmPasswordController@confirm')->name('signUp');
    // Password confirm Routes...
//    Route::get('password/confirm', 'ForgotPasswordController@showLinkRequestForm')->name('password.confirm.request');
//    Route::post('password/confirm', 'ForgotPasswordController@showLinkRequestForm')->name('password.confirm');
    // Password Reset Routes...
    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');

});

