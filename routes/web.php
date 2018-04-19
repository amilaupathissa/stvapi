<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return "Namo Buddhaya..!";
});

Route::group(['prefix'=>'latest_videos'],function(){
    Route::get('/youtube/{count?}','Post\PostController@youtube_videos')->where('count', '[0-9]+');
    Route::get('/facebook/{count?}','Post\PostController@facebook_videos')->where('count', '[0-9]+');
    Route::get('/vimeo/{count?}','Post\PostController@vimeo_videos')->where('count', '[0-9]+');
    Route::get('/{count?}','Post\PostController@latest_videos_count')->where('count', '[0-9]+');
});

Route::get('video/{id}', 'Post\PostController@singleVideo')->where('id', '[0-9]+');
