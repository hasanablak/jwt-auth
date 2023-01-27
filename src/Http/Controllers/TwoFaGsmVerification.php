<?php

namespace Hasanablak\JwtAuth\Http\Controllers;

use	App\Http\Controllers\Controller;
use Hasanablak\JwtAuth\Http\Interfaces\IForSendSms;
use	Illuminate\Http\Request;

use	Hasanablak\JwtAuth\Models\LogSendOut;
use Hasanablak\JwtAuth\Http\Resources\AuthResource;

class TwoFaGsmVerification extends Controller
{
	public $sendSms;

	public function __construct(IForSendSms $sendSms)
	{
		$this->sendSms = $sendSms;

		$this->sendSms->type = '8';
	}

	public function	codeSend(Request $request)
	{
		try {
			$request->validate(["type" => "required|in:" . $this->sendSms->type]);

			$settings = auth('api')->user()->settings;

			$data["randomCode"]			= rand(100000, 999999);
			$data["number"]				= $settings->gsm_dial_code . str_replace(' ', '', $settings->gsm);
			$data["message"]			= "Your verification code is " . $data["randomCode"];
			$data["status"]				= "waiting";

			$response	= $this->sendSms->send($data);

			if ($response->status == "error") {
				return $this->sendSms->fails($response->message);
			} else {
				// TODO: KOD GÖNDERİMİ SAĞLANDIKTAN SONRA İLGİLİ log_mailQid'nin status'unu approved'dan used gibi bir şeye
				// çevrilebilir
				// neden?
				// çünkü, yeni telefon numarasına kod gönderebilmenin iki yolu var
				// 1 şuanda telefon numaran olmayacak
				// 2 şuanda bir telefon numarası kullanılıyor ise ona gönderilmiş bir kodun log_mailQid'nin statusunun approved
				// olması gerekiyor
				return response($this->sendSms->success("ok", [
					"log_send_out_id" => $response->log_send_out_id
				]));
			}
		} catch (\Exception	$e) {
			return response($this->sendSms->fails($e->getMessage()));
		}
	}

	public function codeConfirm(Request	$request)
	{
		$request->validate([
			"log_send_out_id"	=>	"required",
			"code"			=>	"required",
			"type" => "required|in:" . $this->sendSms->type
		]);

		try {
			$log_mailQuery = LogSendOut::where('user_id',	auth('api')->id())
				->where('type_id', $this->sendSms->type)
				->where('id', $request->log_send_out_id)
				->whereJsonContains('data->status',	'waiting')
				->whereJsonContains('data->randomCode',	intval($request->code));

			$log_mailQuery->firstOrFail();

			$log_mailQuery->update(["data->status" => "finished"]);

			$token = auth('api')->claims(["two_fa_gsm_status" => "1"])->refresh();

			return response(new AuthResource($token));
		} catch (\Exception	$e) {
			return response($this->sendSms->fails($e->getMessage()));
		}
	}
}
