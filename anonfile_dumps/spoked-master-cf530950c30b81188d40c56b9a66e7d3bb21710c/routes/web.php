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

use Illuminate\Support\Facades\Route;

Route::get('login', ['as' => 'login', 'uses' => 'Auth\LoginController@showLoginForm']);
Route::post('login', ['as' => 'login.post', 'uses' => 'Auth\LoginController@login']);
Route::get('logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);

Route::group(['middleware' => 'angular'], function () {
    Route::post('/api/macros/add', 'Angular\Macros@add')->name('macros_add');
    Route::get('/api/macros/get/{id?}', 'Angular\Macros@get')->name('macros_get');
    Route::get('/api/macros/del/{id}', 'Angular\Macros@del')->name('macros_del');

    Route::post('/api/macros/global/upd', 'Angular\Macros@global_upd')->name('macros_global_upd');
    Route::get('/api/macros/global', 'Angular\Macros@global_get')->name('macros_global_get');

    Route::get('/api/emails/state/active/get', 'Angular\Emails@state_active_get')->name('emails_state_active_get');
    Route::get('/api/emails/state/active/set/{id}', 'Angular\Emails@state_active_set')->name('emails_state_active_set');
    Route::get('/api/emails/proxy/check', 'Angular\Emails@proxy_check')->name('emails_proxy_check');
});

Route::group(['middleware' => 'auth'], function () {
    Route::match(['post', 'get'], '/', 'Emails@index')->name('emails_index');
    Route::match(['post', 'get'], '/emails/edit/{id?}', 'Emails@edit')->name('emails_edit');
    Route::match(['post', 'get'], '/list', 'Files@list_index')->name('files_list_index');
    Route::match(['post', 'get'], '/list/edit/{id?}', 'Files@list_edit')->name('files_list_edit');
    Route::match(['post', 'get'], '/list/files/{id}', 'Files@files_index')->name('files_list');
    Route::match(['post', 'get'], '/list/files/add/{id}', 'Files@files_add')->name('files_add');

    Route::match(['post', 'get'], '/config/database', 'Configs@index')->name('config_database');
    Route::match(['post', 'get'], '/config/mail_proxies', 'Configs@mail_proxies')->name('config_mail_proxies');
    Route::match(['post', 'get'], '/config/general', 'Configs@general')->name('config_general');
    Route::match(['post', 'get'], '/config/global_macros', 'Configs@global_macros')->name('config_global_macros');
    Route::match(['post', 'get'], '/config/blacklist', 'Configs@blacklist')->name('config_blacklist');
    Route::match(['post', 'get'], '/config/web_hosts', 'Webhosts@indexHosts')->name('config_web_hosts');
    Route::match(['post', 'get'], '/config/web_hosts/edit/{id?}', 'Webhosts@editHost')->name('config_edit_web_hosts');
    Route::get('/config/script_types', 'Webhosts@indexTypes')->name('config_script_types');

    Route::match(['post', 'get'], '/macros', 'Macros@index')->name('macros');
    Route::match(['post', 'get'], '/macros/edit/{id?}', 'Macros@edit')->name('macros_edit');

    // Route::get('/statistics', 'Statistics@index')->name('statistics_index');
    Route::match(['post', 'get'], '/statistics/{id}', 'Statistics@get')->name('statistics_get');
});