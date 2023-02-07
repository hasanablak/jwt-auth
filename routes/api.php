<?php

use Hasanablak\JwtAuth\Http\Middleware\LocalizationMiddleware;
use Illuminate\Support\Facades\Route;
use Hasanablak\JwtAuth\Http\Controllers\AuthController;
use Hasanablak\JwtAuth\Http\Controllers\SignUpMailVerification;
use Hasanablak\JwtAuth\Http\Controllers\SignUpGsmVerification;
use Hasanablak\JwtAuth\Http\Controllers\TwoFaMailVerification;
use Hasanablak\JwtAuth\Http\Controllers\TwoFaGsmVerification;
use Hasanablak\JwtAuth\Http\Middleware\BasicTokenMiddleware;
use Hasanablak\JwtAuth\Http\Middleware\TwoFaValidateMiddleware;
use Hasanablak\JwtAuth\Http\Controllers\UserController;
use Hasanablak\JwtAuth\Http\Middleware\LogSendOutThrottleMiddleware;
use Hasanablak\JwtAuth\Http\Controllers\ChangeEmailController;
use Hasanablak\JwtAuth\Http\Controllers\ChangeGsmController;

Route::group(['prefix' => 'api', 'middleware' => [LocalizationMiddleware::class]], function () {
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

	Route::get('auth/settings', [AuthController::class, "settingsGet"])
		->middleware([BasicTokenMiddleware::class, TwoFaValidateMiddleware::class])
		->name('auth.update');

	Route::patch('auth/settings', [AuthController::class, "settingsUpdate"])
		->middleware([BasicTokenMiddleware::class, TwoFaValidateMiddleware::class])
		->name('auth.settings.update');

	Route::get('users/{username}', [UserController::class, "show"])
		->middleware([BasicTokenMiddleware::class, TwoFaValidateMiddleware::class]);

	Route::post('email/sign-up-verification/code-send', [SignUpMailVerification::class, "codeSend"])
		->name("email.sign-up-verification.code-send")
		//->middleware([BasicTokenMiddleware::class, LogSendOutThrottleMiddleware::class]);
		->middleware([BasicTokenMiddleware::class]);

	Route::post('email/sign-up-verification/code-confirm', [SignUpMailVerification::class, "codeConfirm"])
		->name("email.sign-up-verification.code-confirm")
		->middleware(BasicTokenMiddleware::class);


	Route::post('gsm/sign-up-verification/code-send', [SignUpGsmVerification::class, "codeSend"])
		->name("gsm.sign-up-verification.code-send")
		//->middleware([BasicTokenMiddleware::class, LogSendOutThrottleMiddleware::class]);
		->middleware([BasicTokenMiddleware::class]);

	Route::post('gsm/sign-up-verification/code-confirm', [SignUpGsmVerification::class, "codeConfirm"])
		->name("gsm.sign-up-verification.code-confirm")
		->middleware(BasicTokenMiddleware::class);


	Route::post('email/two-fa-verification/code-send', [TwoFaMailVerification::class, "codeSend"])
		->middleware(BasicTokenMiddleware::class);

	Route::post('email/two-fa-verification/code-confirm', [TwoFaMailVerification::class, "codeConfirm"])
		->middleware(BasicTokenMiddleware::class);


	Route::post('gsm/two-fa-verification/code-send', [TwoFaGsmVerification::class, "codeSend"])
		->middleware(BasicTokenMiddleware::class);

	Route::post('gsm/two-fa-verification/code-confirm', [TwoFaGsmVerification::class, "codeConfirm"])
		->middleware(BasicTokenMiddleware::class);


	Route::group([
		'prefix' => 'change-email',
		'middleware' => [
			TwoFaValidateMiddleware::class
		]
	], function () {
		Route::post('send-code-to-current-email', [ChangeEmailController::class, 'sendCodeToCurrentEmail']);
		//->middleware(LogSendOutThrottleMiddleware::class);
		Route::post('confirm-current-email-sent-code', [ChangeEmailController::class, 'confirmCurrentEmailSentCode']);
		Route::post('send-code-to-new-email', [ChangeEmailController::class, 'sendCodeToNewEmail']);
		//->middleware(LogSendOutThrottleMiddleware::class);
		Route::post('confirm-new-email-sent-code', [ChangeEmailController::class, 'confirmNewEmailSentCode']);
		Route::post('update', [ChangeEmailController::class, 'update']);
	});

	Route::group([
		'prefix' => 'change-gsm',
		'middleware' => [
			TwoFaValidateMiddleware::class
		]
	], function () {
		Route::post('send-code-to-current-gsm', [ChangeGsmController::class, 'sendCodeToCurrentGsm']);
		//->middleware(LogSendOutThrottleMiddleware::class);
		Route::post('confirm-current-gsm-sent-code', [ChangeGsmController::class, 'confirmCurrentGsmSentCode']);
		Route::post('send-code-to-new-gsm', [ChangeGsmController::class, 'sendCodeToNewGsm']);
		//->middleware(LogSendOutThrottleMiddleware::class);
		Route::post('confirm-new-gsm-sent-code', [ChangeGsmController::class, 'confirmNewGsmSentCode']);
		Route::post('update', [ChangeGsmController::class, 'update']);
	});
});
