<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// Route::middleware('throttle:5,1')->post('sendmail', 'Message\MessageController@mailSend');
// Route::group(['middleware' => 'throttle:3,1'], function() {
//     Route::post('sendmail', 'Message\MessageController@mailSend');
// });

Route::post('sendmail', 'Message\MessageController@mailSend');

Route::get('sendmail', function(){
    return 'please use post request instead of get';
});
// Route::post('sendmail','Message\MessageController@mailSend');