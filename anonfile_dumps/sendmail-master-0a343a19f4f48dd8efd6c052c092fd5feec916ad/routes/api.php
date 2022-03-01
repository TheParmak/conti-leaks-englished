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

// API sendmail2
Route::get('/client_ip', 'Clients@getClientIp');
Route::get('/client_ip/all', 'Clients@getClientIpAll');
Route::post('/new_client', 'Clients@setClientInfo');

// API sendmail
Route::get('/new_client/{base64}', 'Clients@setClientIp');
Route::get('/dnsbl', 'Clients@getBlackList');
Route::get('/whitelist', 'Clients@getWhiteList');

// API sendmail random
Route::post('/file', 'Clients@getFile');