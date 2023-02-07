<?php

namespace Hasanablak\JwtAuth\Http\Controllers;

use Hasanablak\JwtAuth\Http\Interfaces\IForSendSms;
use Hasanablak\JwtAuth\Http\Resources\AuthResource;
use Hasanablak\JwtAuth\Models\Notification as ModelsNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hasanablak\JwtAuth\Models\User;
use Hasanablak\JwtAuth\Notifications\YourPasswordHasBeedChanged;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use Hasanablak\JwtAuth\Models\UserSetting;
//use App\Events\UserRegister;
//use App\Supports\PrepareForUserCreate;
//use App\Repository\IUserRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Hasanablak\JwtAuth\Notifications\ResetPassword;

class AuthController extends Controller
{
	public $sendSms;

	public function __construct(IForSendSms $sendSms)
	{
		$this->sendSms = $sendSms;
	}
	public function register(Request $request)
	{
		$request->validate([
			'email'			=> [Rule::requiredIf(fn () => !request()->has('gsm')), 'email', 'max:64', 'unique:users'],
			'password'		=> 'required|string|min:6|confirmed',
			'name'			=> 'required|string',
			'surname'		=> 'required|string',
			'gsm'			=> [
				Rule::requiredIf(fn () => !request()->has('email')),
				'unique:users',
				//'numeric',
				'regex:/^([0-9\s\-\+\(\)]*)$/',
				'size:12'
			]
		]);

		//$request['email'] = $request->email ?: trim($request->gsm_dial_code . $request->gsm);

		User::create($request->all());

		/*
		foreach ($request->only(['gsm', 'gsm_dial_code']) as $key => $value) {
			UserSetting::create([
				"user_id"	=>	$user->id,
				"key"		=>	$key,
				"value"		=>	$value
			]);
		}
		*/


		//$user = $this->userRepository->create($request->all());

		//event(new UserRegister($user));
		// BURASI TEST EDİLMESİ LAZIM
		/* 
			ADAMIN E-POSTASI VE TELEFON NUMARASI VAR İSE VE 
			AŞAĞIDAKİ GİBİ Bİ DURUM VAR VE KULLANICI SADECE EPOSTAYI YA DA SADECE GSM&DIAL_CODE GİRDİ NE OLUYOR? 
		*/
		$credentials = $request->only('email', 'gsm', 'gsm_dial_code', 'password');

		$token = auth('api')->attempt($credentials);
		return response(new AuthResource($token));
	}

	public function login(Request $request)
	{
		$request->validate([
			'email' => [
				Rule::requiredIf(fn () => !request()->has('gsm') && !request()->has('username')),
				'email'
			],
			'gsm' => [
				Rule::requiredIf(fn () => !request()->has('email') && !request()->has('username')),
				'regex:/^([0-9\s\-\+\(\)]*)$/',
				'size:12'
			],
			'username' => [
				Rule::requiredIf(fn () => !request()->has('gsm') && !request()->has('email')),
				'min:3'
			],
			'password' => 'required|string|min:6',
		]);
		$credentials = $request->only('username', 'email', 'gsm', 'password');

		$token = auth('api')->attempt($credentials);

		return response(new AuthResource($token));
	}

	public function forgotPassword(Request $request)
	{
		$request->validate([
			'email' => [
				Rule::requiredIf(fn () => !request()->has('gsm')),
				'exists:users,email',
				'email'
			],
			'gsm' => [
				Rule::requiredIf(fn () => !request()->has('email')),
				'exists:users,gsm',
				'regex:/^([0-9\s\-\+\(\)]*)$/',
				'size:12'
			]
		]);

		$user = User::where('email', $request->email)
			->orWhere('gsm', $request->gsm)
			->first();

		$user->notify(new ResetPassword($request->has('email') ? 'mail' : 'sms'));

		return response([
			"status"	=>	'success'
		]);
	}

	public function resetPassword(Request $request)
	{
		$request->validate([
			'code' => 'required',
			'password' => 'required|min:8|confirmed',
			'email' => [
				Rule::requiredIf(fn () => !request()->has('gsm')),
				'exists:users,email',
				'email'
			],
			'gsm' => [
				Rule::requiredIf(fn () => !request()->has('email')),
				'exists:users,gsm',
				'regex:/^([0-9\s\-\+\(\)]*)$/',
				'size:12'
			]
		]);

		$userQuery = User::select('id')->where('email', $request->email)
			->orWhere('gsm', $request->gsm);
		$user = $userQuery->first();
		$notificationQuery = ModelsNotification::control(
			type: 'Hasanablak\JwtAuth\Notifications\ResetPassword',
			status: 'waiting',
			code: $request->code,
			notifiable_id: $user->id
		);

		$notificationQuery->firstOrFail();

		$notificationQuery->update(["data->status" => "finished"]);

		$user->password = $request->password;
		$user->save();


		$user->notify(new YourPasswordHasBeedChanged(
			channel: $request->has('email') ? 'mail' : 'sms',
			browser: $request->header('User-Agent'),
			ip: $request->ip()
		));


		return response([
			"status"	=>	'success'
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
			//"password"	=> ['confirmed', 'string', 'min:6', 'current_password:api'],
			"password"	=> ['confirmed', 'string', 'min:6'],
			"current_password" => ['current_password:api', 'required_with:password']
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


	public function settingsGet()
	{
		return response([
			"status"	=>	"success",
			"data"		=>	auth('api')->user()->settingsAll
		]);
	}

	public function settingsUpdate(Request $request)
	{
		#TODO: Request oluşturulacak, kullanıcının gönderdiği veriler kontrol edilecek.
		$reqData = $request->all();

		foreach ($reqData as $key => $value) {
			UserSetting::updateOrCreate(
				[
					"user_id"	=> auth('api')->id(),
					"key"		=> $key
				],
				[
					"value"		=> $value
				]
			);
		}

		return response([
			"status"	=> "success"
		]);
	}
}
