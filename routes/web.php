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

Route::get('/admin/logout',['uses'=>'Admin\AuthController@logout','as'=>'admin.auth.logout']);
Route::get('/admin/login', ['uses'=>'Admin\AuthController@showLoginForm','as'=>'admin.auth.login']);
Route::post('/admin/login', 'Admin\AuthController@login');
// all protected middleare routes goes here...
Route::middleware('admin')->group( function () {
	Route::get('/admin', 'AdminController@index')->name('admin');
});

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
