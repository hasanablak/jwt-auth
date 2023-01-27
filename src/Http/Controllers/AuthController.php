<?php

namespace Hasanablak\JwtAuth\Http\Controllers;

use Hasanablak\JwtAuth\Http\Resources\AuthResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hasanablak\JwtAuth\Models\User;
use Hasanablak\JwtAuth\Notifications\YourPasswordHasBeedChanged;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
//use App\Events\UserRegister;
//use App\Supports\PrepareForUserCreate;
//use App\Repository\IUserRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{

	public function register(Request $request)
	{
		$request->validate([
			'email'		=> 'required|string|email|max:50|unique:users',
			'password'	=> 'required|string|min:6|confirmed',
			'name'		=> 'required|string',
			'surname'	=> 'required|string'
		]);

		$user = User::create($request->all());

		//$user = $this->userRepository->create($request->all());

		//event(new UserRegister($user));

		$credentials = $request->only('email', 'password');
		/*
		try {
			$user = auth()->userOrFail();
		} catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
			// do something
		}
		*/
		$token = auth('api')->attempt($credentials);
		return response(new AuthResource($token));

		return response([
			"not"	=>	"Jwt'ye göre user verilecek ya da verilmeyecek",
			"no2"	=>	"JwtAuth'dan geldim ben sa",
			"user"	=>	$user,
			"token"	=>	$token
		]);
	}

	public function login(Request $request)
	{
		$request->validate([
			'email' => 'required|string|email',
			'password' => 'required|string',
		]);
		$credentials = $request->only('email', 'password');
		/*
		try {
			$user = auth()->userOrFail();
		} catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
			// do something
		}
		*/
		$token = auth('api')->attempt($credentials);

		return response(new AuthResource($token));
	}

	public function forgotPassword(Request $request)
	{
		$request->validate(['email' => 'required|email|exists:users,email']);

		#DB::table('password_resets')->delete();

		//https://github.com/laravel/framework/blob/9.x/src/Illuminate/Auth/Passwords/PasswordBroker.php#L48
		//framework\src\Illuminate\Auth\Passwords\PasswordBroker.php:48

		$status = Password::sendResetLink($request->only('email'), function ($user, $token) {
			$user->sendPasswordResetNotification($token);
		});



		return response([
			"status"	=>	$status === Password::RESET_LINK_SENT ? 'success' : 'error',
			"message"	=>	__($status)
		]);
	}

	public function resetPassword(Request $request)
	{
		$request->validate([
			'token' => 'required',
			'email' => 'required|email',
			'password' => 'required|min:8|confirmed',
		]);

		$status = Password::reset(
			$request->only('email', 'password', 'password_confirmation', 'token'),

			function ($user, $password) use ($request) {
				$user->forceFill([
					'password' => $password
				])->setRememberToken(Str::random(60));

				$user->save();

				Notification::send($user, new YourPasswordHasBeedChanged($request->header('User-Agent'), $request->ip()));
			}
		);

		return response([
			"status"	=>	$status === Password::PASSWORD_RESET ? 'success' : 'error',
			"message"	=>	__($status)
		]);
	}

	public function show()
	{
		return response([
			"status"	=>	"success",
			"data"		=>	[
				...auth('api')->user()->toArray(),
				"settings" => auth('api')->user()->settingsAll
			]
		]);
	}

	public function update(Request $request)
	{
		$request->validate([
			"avatar"	=> ['max:5000'],
			"name"		=> ['required'],
			"surname"	=> ['required'],
			"username"	=> ['required', "unique:users,username," . auth('api')->id()],
			"password"	=> ['confirmed', 'string', 'min:6', 'current_password:api'],
			//"current_password" => ['current_password:api', 'required_with:password']
		]);

		if ($request->avatar) {
			$realName = Storage::disk('avatar')->put('', $request->avatar);

			User::where('id', auth('api')->id())
				->update([
					"avatar" =>  "/storage/avatar/" . $realName,
				]);
		}

		if ($request->password) {
			User::find(auth('api')->id())
				->update([
					"password"	=>	$request->password
				]);
		}

		User::where('id', auth('api')->id())
			->update([
				"name"			=> $request->name,
				"surname"		=> $request->surname,
				"username"		=> $request->username
			]);

		return response([
			"status"	=> "success",
			"data"		=> [
				...User::find(auth('api')->id())->toArray(),
				"settings" => auth('api')->user()->settingsAll
			]
		]);
	}
}
