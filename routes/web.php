<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\DB;
use App\Jobs\UpdateDB;
use App\Models\HistoryWeather;
use App\Models\HistoryTreeDayWeather;
use App\Models\HistoryAllWeather;


Route::get('/', function () {
    return view('index');
});

Route::get('/can', 'App\Http\Controllers\PostController@getWeather');


Route::get('/cans', 'App\Http\Controllers\PostController@getTimeRange');  
Route::get('/getChange', 'App\Http\Controllers\PostController@getChangeWeather'); 
   
                     
      
Route::get('/test', function(){
   
});
