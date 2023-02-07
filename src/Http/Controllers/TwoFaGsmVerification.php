<?php

namespace Hasanablak\JwtAuth\Http\Controllers;

use	App\Http\Controllers\Controller;
use	Illuminate\Http\Request;
use Hasanablak\JwtAuth\Http\Resources\AuthResource;
use Hasanablak\JwtAuth\Notifications\TwoFaGsm;
use Hasanablak\JwtAuth\Models\Notification;

class TwoFaGsmVerification extends Controller
{

	public function codeSend()
	{
		auth('api')->user()->notify(new TwoFaGsm);

		return response([
			"status"	=>	"success"
		]);
	}

	public function codeConfirm(Request $request)
	{
		$request->validate([
			"code"			=>	"required"
		]);

		$logSendOutQuery = Notification::control(
			type: 'Hasanablak\JwtAuth\Notifications\TwoFaGsm',
			status: 'waiting',
			code: $request->code,
			notifiable_id: auth('api')->id()
		);

		$logSendOutQuery->firstOrFail();

		$logSendOutQuery->update(["data->status" => "finished"]);


		$token = auth('api')->claims(["two_fa_gsm_status" => "1"])->refresh();

		return response(new AuthResource($token));
	}
}
