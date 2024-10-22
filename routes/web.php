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
Route::get('pdrb_ringkasan1/{id}', 'TabelRingkasanController@ringkasan1');
Route::get('pdrb_ringkasan2/{id}', 'TabelRingkasanController@ringkasan2');
Route::get('pdrb_ringkasan3/{id}', 'TabelRingkasanController@ringkasan3');
Route::get('pdrb_ringkasan4/{id}', 'TabelRingkasanController@ringkasan4');
Route::get('pdrb_ringkasan5/{id}', 'TabelRingkasanController@ringkasan5');
Route::get('upload/fenomena_import', 'UploadController@fenomena_upload');
Route::post('upload/fenomena_import', 'UploadController@fenomena_import');

Route::get('tabel/resume', 'ResumeController@index');
Route::post('tabel/resume', 'ResumeController@get');

Route::get('revisi/total', 'RevisiTotalController@index');
Route::post('revisi/total', 'RevisiTotalController@get');
