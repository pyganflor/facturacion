<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\{
    Route,
    Auth
};


Route::get('/', 'HomeController@index');

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');
