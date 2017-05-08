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

/*Route::get('/', function () {
    return view('welcome');
});*/
Route::get('/', 'Test@index');

Route::get('download', 'FileController@download');
Route::match(['get', 'post'], 'share', 'ShareController@view');
Route::match(['get', 'post'], 'getDownloadLinkFromShare', 'ShareController@getDownloadLinkFromShare');

// Route::group(['middleware' => ['web']], function () {
Route::group([], function () {
    Route::group(['prefix' => 'v1/passport'], function () {
        /*Route::get('users', function () {
            // 匹配 "/admin/users" URL
        });*/
        Route::match(['get', 'post'], 'login', 'PassportController@login');
        Route::match(['get', 'post'], 'logout', 'PassportController@logout');
        Route::match(['get', 'post'], 'register', 'PassportController@register');
        Route::match(['get', 'post'], 'changePassword', 'PassportController@changePassword');
        Route::match(['get', 'post'], 'sendEmail', 'PassportController@sendEmail');
        // Route::match(['get', 'post'], 'set', 'PassportController@set');
        // Route::match(['get', 'post'], 'get', 'PassportController@get');
    });

    Route::group(['prefix' => 'v1/file', 'middleware' => ['VerifyIdentity'] ], function () {
        Route::match(['get', 'post'], 'explorer', 'FileController@explorer');
        Route::match(['get', 'post'], 'search', 'FileController@search');
        Route::match(['get', 'post'], 'upload', 'FileController@upload');
        Route::match(['get', 'post'], 'deleteFiles', 'FileController@deleteFiles');
        Route::match(['get', 'post'], 'deleteFolders', 'FileController@deleteFolders');
        Route::match(['get', 'post'], 'move', 'FileController@move');
        Route::match(['get', 'post'], 'createFile', 'FileController@createFile');
        Route::match(['get', 'post'], 'createFloder', 'FileController@createFloder');
        Route::match(['get', 'post'], 'getDownloadLink', 'FileController@getDownloadLink');
        Route::match(['get', 'post'], 'emptyTrash', 'FileController@emptyTrash');
    });

    Route::group(['prefix' => 'v1/share', 'middleware' => ['VerifyIdentity'] ], function () {
        Route::match(['get', 'post'], 'share', 'ShareController@share');
        Route::match(['get', 'post'], 'shareFile', 'ShareController@shareFile');
        Route::match(['get', 'post'], 'shareFloder', 'ShareController@shareFloder');
        Route::match(['get', 'post'], 'deleteShare', 'ShareController@deleteShare');
    });

    Route::group(['prefix' => 'v1/recycle', 'middleware' => ['VerifyIdentity'] ], function () {
        Route::match(['get', 'post'], 'view', 'RecycleController@view');
        Route::match(['get', 'post'], 'clear', 'RecycleController@clear');
        Route::match(['get', 'post'], 'recover', 'RecycleController@recover');
        Route::match(['get', 'post'], 'deleteFolders', 'RecycleController@deleteFolders');
        Route::match(['get', 'post'], 'deleteFiles', 'RecycleController@deleteFiles');
        Route::match(['get', 'post'], 'recoverFolders', 'RecycleController@recoverFolders');
        Route::match(['get', 'post'], 'recoverFiles', 'RecycleController@recoverFiles');
    });
});