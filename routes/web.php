<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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
    return view('welcome');
});


Route::get('upload/import', 'UploadController@upload');
Route::post('upload/import', 'UploadController@import');
<<<<<<< HEAD
Route::get('upload/fenomena_import', 'UploadController@fenomena_upload');
Route::post('upload/fenomena_import', 'UploadController@fenomena_import');
=======

Route::get('tabel/resume', 'ResumeController@index');
Route::post('tabel/resume', 'ResumeController@get');
<<<<<<< HEAD
>>>>>>> afb2b8133c8b6e738511b5689c10a935ae331612
=======

Route::get('revisi/total', 'RevisiTotalController@index');
Route::post('revisi/total', 'RevisiTotalController@get');
>>>>>>> origin/main
