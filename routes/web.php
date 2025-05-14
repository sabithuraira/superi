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

Auth::routes();

Route::get('home', 'HomeController@index');
// Route::group(['middleware' => ['role:superadmin']], function () {
Route::group(['middleware' => 'auth'], function () {
    Route::get('authorization/set_my_role', 'AuthorizationController@set_my_role');
    Route::get('beranda', 'HomeController@beranda');
});

Route::group(['middleware' => ['role:superadmin']], function () {
    //SPATIE
    Route::get('authorization/permission', 'AuthorizationController@permission');
    Route::post('authorization/permission', 'AuthorizationController@permission_store');

    Route::get('authorization/role', 'AuthorizationController@role');
    Route::get('authorization/{id}/role_edit', 'AuthorizationController@role_edit');
    Route::post('authorization/role', 'AuthorizationController@role_store');

    Route::get('authorization/user', 'AuthorizationController@user');
    Route::get('authorization/{id}/user_edit', 'AuthorizationController@user_edit');
    Route::post('authorization/user', 'AuthorizationController@user_update');

    Route::get('setting_app', 'SettingAppController@index');
    Route::post('setting_app', 'SettingAppController@store');
});

// Route::group(['middleware' => ['role:superadmin','permission:import_pdrb']], function () {
Route::group(['middleware' => ['permission:import_pdrb']], function () {
    Route::get('upload/import', 'UploadController@upload')->name('upload/import');
    Route::post('upload/import', 'UploadController@import');
});

Route::group(['middleware' => ['permission:import_fenomena']], function () {
    Route::get('upload/fenomena_import', 'UploadController@fenomena_upload');
    Route::post('upload/fenomena_import', 'UploadController@fenomena_import');
    Route::post('upload/fenomena', 'UploadController@fenomena');
});

Route::group(['middleware' => ['permission:tabel_ringkasan']], function () {
    Route::get('pdrb_ringkasan1/{id}', 'TabelRingkasanController@ringkasan1');
    Route::get('pdrb_ringkasan2/{id}', 'TabelRingkasanController@ringkasan2');
    Route::get('pdrb_ringkasan3/{id}', 'TabelRingkasanController@ringkasan3');
    Route::get('pdrb_ringkasan4/{id}', 'TabelRingkasanController@ringkasan4');
    Route::get('pdrb_ringkasan5/{id}', 'TabelRingkasanController@ringkasan5');
    Route::get('pdrb_ringkasan6/{id}', 'TabelRingkasanController@ringkasan6');
    Route::get('pdrb_ringkasan_export_all', 'TabelRingkasanController@export_all');
});

Route::group(['middleware' => ['permission:tabel_resume']], function () {
    Route::get('tabel/resume', 'ResumeController@index');
    Route::post('tabel/resume', 'ResumeController@get');
    Route::post('tabel/resume/export', 'ResumeController@export');
    Route::post('tabel/resume/exportall', 'ResumeController@export_all');

    Route::get('pdrb_resume/{id}', 'ResumeController2@index');
});

Route::group(['middleware' => ['permission:tabel_kabkot']], function () {
    Route::get('pdrb_kabkot/{id}', 'TabelKabkotController@kabkot');
    Route::get('pdrb_kabkot_7pkrt/{id}', 'TabelKabkotController@kabkot_7pkrt');
    Route::get('pdrb_kabkot_brs/{id}', 'TabelKabkotController@kabkot_brs');
    Route::get('pdrb_kabkot_rilis/{id}', 'TabelKabkotController@kabkot_rilis');
});

Route::group(['middleware' => ['permission:tabel_history']], function () {
    Route::get('pdrb_putaran/{id}', 'PdrbPutaranController@index');
});

Route::group(['middleware' => ['permission:arah_revisi_total']], function () {
    Route::get('revisi/total', 'RevisiTotalController@index');
    Route::post('revisi/total', 'RevisiTotalController@get');
    Route::post('revisi/total/export', 'RevisiTotalController@export');
    Route::post('revisi/total/exportall', 'RevisiTotalController@export_all');

    Route::get('revisi_total/{id}', 'RevisiTotalController2@index');
});

Route::group(['middleware' => ['permission:arah_revisi_kabkota']], function () {
    Route::get('revisi_kabkot/{id}', 'RevisiKabkotController@index');
    Route::get('revisi_kabkot_7pkrt/{id}', 'RevisiKabkotController@revisi_7pkrt');
    Route::get('revisi_kabkot_rilis/{id}', 'RevisiKabkotController@revisi_rilis');
});


Route::group(['middleware' => ['permission:fenomena_total']], function () {});

Route::group(['middleware' => ['permission:fenomena_kabkota']], function () {});

Route::group(['middleware' => 'auth'], function () {
    Route::post('upload/pdrb', 'UploadController@pdrb');
    Route::post('upload/is_all_approve', 'UploadController@isAllApprove');
    Route::post('upload/fenomena', 'UploadController@fenomena');
});


Route::group(['middleware' => ['role:approval_admin']], function () {
    Route::post('upload/approve_admin', 'UploadController@approve_admin');
});
