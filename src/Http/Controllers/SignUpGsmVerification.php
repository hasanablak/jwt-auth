<?php

namespace Hasanablak\JwtAuth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hasanablak\JwtAuth\Http\Resources\AuthResource;
use Hasanablak\JwtAuth\Notifications\RegistrationGsmVerification;
use Hasanablak\JwtAuth\Models\Notification;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SignUpGsmVerification extends Controller
{

	public function codeSend()
	{
		auth('api')->user()->notify(new RegistrationGsmVerification());

		return response([
			"status"	=>	"success"
		]);
	}

	public function codeConfirm(Request $request)
	{
		$request->validate([
			"code"			=>	"required"
		]);


		$logSendOutQuery =
			Notification::control(
				type: 'Hasanablak\JwtAuth\Notifications\RegistrationGsmVerification',
				status: 'waiting',
				code: $request->code,
				notifiable_id: auth('api')->id()
			);


		$logSendOutQuery =
			Notification::control(
				type: 'Hasanablak\JwtAuth\Notifications\RegistrationGsmVerification',
				status: 'waiting',
				code: $request->code,
				notifiable_id: auth('api')->id()
			);

		$logSendOutQuery->firstOrFail();

		$logSendOutQuery->update(["data->status" => "finished"]);

		auth('api')->user()->gsm_verified_at = date('Y-m-d H:i:s');

		auth('api')->user()->save();

		$token = auth('api')->claims(["gsm_verify_status" => "1"])->refresh();

		return response(new AuthResource($token));
	}
}
