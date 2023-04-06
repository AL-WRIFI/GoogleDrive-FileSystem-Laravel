<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\DriveController;
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
Route::get('/index', function () {
    return view('var');
});
// Route::post('/pick-file', [DriveController::class, 'pickFile'])->name('pick-file');
// Route::get('index/user',[DriveController::class,'showPicker'])->name('index.user');
Route::get('google/login',[GoogleDriveController::class,'googleLogin'])->name('google.login');
Route::any('google-drive/file-upload',[GoogleDriveController::class,'googleDriveFilePpload'])->name('file.upload');
