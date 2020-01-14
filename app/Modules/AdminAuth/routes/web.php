<?php

Route::group( [ 'namespace' => 'App\Modules\AdminAuth\Controllers',
    'as' => 'test.',
], function(){
    Route::get('/test', ['uses' => 'TestController@index']);
});
