<?php

Route::group( [ 'namespace' => 'App\Modules\Auth\Controllers',
    'as' => 'test.',
], function(){
    Route::get('/test', ['uses' => 'TestController@index']);
});
