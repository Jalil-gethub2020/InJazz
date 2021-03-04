<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('register', 'API\SignController@register');
Route::post('login', 'API\SignController@login');
Route::post('logout', 'API\SignController@logout');

Route::post('password/forgot', 'API\ForgotPassController@forgot');
Route::post('password/reset', 'API\ForgotPassController@reset');

/*
Route::group(['middleware' => 'auth:API'], function () {
    Route::post('change/password', 'API\PasswordController@change_password');
});
*/

Route::middleware('auth:api')->group(function () {
    Route::ApiResource('/tasks', 'API\TaskController'); // func 0 9 10 11 12

    Route::get('/task/ongoing', 'API\TaskController@ongoingTasks');            // (1) ---->  Show Today's Ongoing Tasks Function
    Route::get('/task/complete', 'API\TaskController@completeTasks');         //  (2) ----> Show Today's Complete Tasks Function
    Route::get('/task/tomorrow', 'API\TaskController@tomorrowTasks');        //   (3) ----> Show Tomorrow Tasks

    Route::post('/task/new/today', 'API\TaskController@newTodayTask');         //  (4) ----> Add New Task in Today's Tasks
    Route::post('/task/new/tomorrow', 'API\TaskController@newTomorrowTask');   //  (5) ----> Add New Task in Tomorrow's Tasks

    Route::post('/task/status/{id}', 'API\TaskController@changeStatusTask');   //  (6) ----> Change Task Status (Complete or Not)
    Route::post('/task/goTomorrow/{id}', 'API\TaskController@goTomorrow');     //  (7) ----> Send Tasks To Tomorrow List
    Route::post('/task/backToday/{id}', 'API\TaskController@backToday');       //  (8) ----> Bring Back A Task To Today List
    Route::post('/task/new', 'API\TaskController@store');                       // (9) ----> Create Any  New Task
    Route::get('/task/show/{id}', 'API\TaskController@show');                  // (10) ----> Show Any Task
    Route::post('/task/update/{id}', 'API\TaskController@update');             // (11) ----> Update  Any Task
    Route::post('/task/delete/{id}', 'API\TaskController@destroy');            // (12) ----> Delete Any Task
});
