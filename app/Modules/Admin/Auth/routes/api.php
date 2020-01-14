<?php

Route::group(['middleware'=>'api', 'prefix' => 'api'], function (){

    Route::get('/hello', function (Request $request) {
        return 'hello world!';
    });

});
