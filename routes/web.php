<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\JsonResponse;
use App\Models\Category;

Route::get('/', function () {
    return view('welcome');
});

//register page
Route::get('/register', [RegisterController::class, 'register']);
Route::get('/forgot_pwd', [RegisterController::class, 'forgot_pwd']);
Route::post('/register-process', [RegisterController::class, 'register_process']);
Route::get('/captcha', [RegisterController::class, 'generate']);
Route::post('/store-face', [RegisterController::class, 'storeFace']);

//login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
// Route::get('/dashboard', function () {
//     return view('dashboard.dashboard');
// })->middleware('auth');
Route::get('/validate_qr', [RegisterController::class, 'validate_qr'])->name('validate_qr');
Route::post('/qr-login', [RegisterController::class, 'qr_login']);
Route::get('/validateFace', [RegisterController::class, 'showFaceLogin']);
Route::post('/validateFace', [RegisterController::class, 'processFaceLogin']);

//logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/')->with('success', 'Logged out successfully.');
})->name('logout');

//forgot password
Route::post('/email-process', [ForgotPasswordController::class, 'verifyUser']);

//dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');
Route::get('/fetch-categories/{type_id}', function ($type_id): JsonResponse {
    return response()->json(
        Category::where('type_id', $type_id)->orderBy('name', 'asc')->get()
    );
})->middleware('auth');

//submit txn
Route::post('/transaction', [DashboardController::class, 'transaction']);
