<?php

use Illuminate\Support\Facades\Route;
use Hasanablak\JwtAuth\Http\Controllers\AuthController;
use Hasanablak\JwtAuth\Http\Controllers\SignUpMailVerification;
use Hasanablak\JwtAuth\Http\Controllers\TwoFaMailVerification;
use Hasanablak\JwtAuth\Http\Controllers\TwoFaGsmVerification;
use Hasanablak\JwtAuth\Http\Middleware\BasicTokenMiddleware;
use Hasanablak\JwtAuth\Http\Middleware\TwoFaValidateMiddleware;
use Hasanablak\JwtAuth\Http\Controllers\UserController;

Route::group(['prefix' => 'api'], function () {
	Route::post('login', [AuthController::class, "login"])->name('login');

	Route::post('register', [AuthController::class, "register"])->name('register');

	Route::post('forgot-password', [AuthController::class, "forgotPassword"])->name('password.reset');

	Route::post('reset-password', [AuthController::class, "resetPassword"])->name('password.update');

	Route::get('auth', [AuthController::class, "show"])
		->middleware([BasicTokenMiddleware::class, TwoFaValidateMiddleware::class])
		->name('auth.show');

	Route::patch('auth', [AuthController::class, "update"])
		->middleware([BasicTokenMiddleware::class, TwoFaValidateMiddleware::class])
		->name('auth.update');

	Route::get('users/{username}', [UserController::class, "show"])
		->middleware([BasicTokenMiddleware::class, TwoFaValidateMiddleware::class]);

	Route::post('email/sign-up-verification/code-send', [SignUpMailVerification::class, "codeSend"])
		->middleware(BasicTokenMiddleware::class);

	Route::post('email/sign-up-verification/code-confirm', [SignUpMailVerification::class, "codeConfirm"])
		->middleware(BasicTokenMiddleware::class);


	Route::post('email/two-fa-verification/code-send', [TwoFaMailVerification::class, "codeSend"])
		->middleware(BasicTokenMiddleware::class);

	Route::post('email/two-fa-verification/code-confirm', [TwoFaMailVerification::class, "codeConfirm"])
		->middleware(BasicTokenMiddleware::class);


	Route::post('gsm/two-fa-verification/code-send', [TwoFaGsmVerification::class, "codeSend"])
		->middleware(BasicTokenMiddleware::class);

	Route::post('gsm/two-fa-verification/code-confirm', [TwoFaGsmVerification::class, "codeConfirm"])
		->middleware(BasicTokenMiddleware::class);
});
