<?php

namespace Hasanablak\JwtAuth\Http\Controllers;

use	Illuminate\Http\Request;
use Hasanablak\JwtAuth\Notifications\ChangeGsmSendCodeToCurrentGsm;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Hasanablak\JwtAuth\Notifications\ChangeGsmSendCodeToNewGsm;
use Hasanablak\JwtAuth\Notifications\YourGsmWasChanged;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Hasanablak\JwtAuth\Models\Notification;

class ChangeGsmController extends Controller
{
	public $notificationTypes =  [
		'new' => 'Hasanablak\JwtAuth\Notifications\ChangeGsmSendCodeToNewGsm',
		'current' => 'Hasanablak\JwtAuth\Notifications\ChangeGsmSendCodeToCurrentGsm'
	];

	public function sendCodeToCurrentGsm()
	{
		if (is_null(auth('api')->user()->gsm)) return response(["status" => "error"]);

		auth('api')->user()->notify(new ChangeGsmSendCodeToCurrentGsm());

		return response([
			"status"	=>	"success"
		]);
	}

	public function confirmCurrentGsmSentCode(Request $request)
	{
		$request->validate([
			"code"			=>	"required"
		]);

		$logSendOutQuery = Notification::control(
			type: $this->notificationTypes['current'],
			status: 'waiting',
			code: $request->code,
			notifiable_id: auth('api')->id()
		);

		$logSendOutQuery->firstOrFail();

		$logSendOutQuery->update(["data->status" => "approved"]);

		return response([
			"status"	=>	"success"
		]);
	}

	public function sendCodeToNewGsm(Request $request)
	{
		$request->validate([
			"currentGsmSendedCode"	=> [
				Rule::requiredIf(fn () => !is_null(auth('api')->user()->gsm))
			],
			"newGsm"				=>	[
				'unique:users,gsm',
				'regex:/^([0-9\s\-\+\(\)]*)$/',
				'size:12'
			]
		]);

		if (!is_null(auth('api')->user()->gsm)) {
			Notification::control(
				type: $this->notificationTypes["current"],
				status: 'approved',
				code: $request->code,
				notifiable_id: auth('api')->id()
			);
		}


		FacadesNotification::route('gsm', $request->newGsm)
			->notify(new ChangeGsmSendCodeToNewGsm());

		return response([
			"status"	=>	"success"
		]);
	}

	public function confirmNewGsmSentCode(Request $request)
	{
		$request->validate([
			"code"			=>	"required"
		]);

		$logSendOutQuery = Notification::control(
			type: $this->notificationTypes["new"],
			status: 'waiting',
			code: $request->code,
			notifiable_id: auth('api')->id()
		);

		$logSendOutQuery->firstOrFail();

		$logSendOutQuery->update(["data->status" => "approved"]);

		return response([
			"status"	=>	"success"
		]);
	}

	public function update(Request $request)
	{

		$request->validate([
			"current"	=> [Rule::requiredIf(fn () => !is_null(auth('api')->user()->gsm))],
			"new"		=>	"required"
		]);
		foreach ($request->only("current", "new") as $index => $sendedCode) {

			$logSendOutQuery = Notification::control(
				type: $this->notificationTypes[$index],
				status: 'approved',
				code: $sendedCode,
				notifiable_id: auth('api')->id()
			);

			$logSendOut = $logSendOutQuery->firstOrFail();

			$logSendOutQuery->update(["data->status" => "finished"]);
		}

		$newGsm = json_decode($logSendOut->data)->newGsm;


		is_null(auth('api')->user()->gsm) ?:
			auth('api')->user()->notify(new YourGsmWasChanged(
				newGsm: $newGsm
			));


		auth('api')->user()->gsm_verified_at = date('Y-m-d H:i:s');
		auth('api')->user()->gsm = $newGsm;
		auth('api')->user()->save();

		return response([
			"status"	=>	"success",
			"message"	=>	__('jwt-auth.change-gsm.your-phone-number-was-changed')
		]);
	}
}
