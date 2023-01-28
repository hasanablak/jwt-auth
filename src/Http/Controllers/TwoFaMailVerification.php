<?php

namespace Hasanablak\JwtAuth\Http\Controllers;

use	App\Http\Controllers\Controller;
use	Illuminate\Http\Request;

use	Hasanablak\JwtAuth\Models\LogSendOut;
use Hasanablak\JwtAuth\Http\Resources\AuthResource;
use Hasanablak\JwtAuth\Http\Interfaces\IForSendMail;

class TwoFaMailVerification extends Controller
{
	public $sendMail;

	public function __construct(IForSendMail $sendMail)
	{
		$this->sendMail = $sendMail;
		$this->sendMail->type = '3';
	}

	public function	codeSend(Request $request)
	{
		try {
			$request->validate(["type" => "required|in:" . $this->sendMail->type]);

			$useData["view"]		= 'jwt-auth::emails.two_fa_email';
			$useData["email"]		= auth('api')->user()->email;
			$useData["title"]		= 'Two Fa Title';
			$useData["subject"]		= 'Two Fa Subject';

			$data["randomCode"]		= rand(100000, 999999);
			$data["status"]			= "waiting";
			$data["email"]			= auth('api')->user()->email;

			$log	= $this->sendMail->send($useData, $data);

			return response($this->sendMail->success("ok", ["log_send_out_id"  => $log->id]));
		} catch (\Exception	$e) {
			return response($this->sendMail->fails($e->getMessage()));
		}
	}

	public function codeConfirm(Request	$request)
	{
		$request->validate([
			"log_send_out_id"	=>	"required",
			"code"			=>	"required"
		]);

		try {

			$log_mailQuery = LogSendOut::where('user_id',	auth('api')->id())
				->where('type_id', $this->sendMail->type)
				->where('id', $request->log_send_out_id)
				->whereJsonContains('data->status',	'waiting')
				->whereJsonContains('data->randomCode',	intval($request->code));

			$log_mailQuery->firstOrFail();

			$log_mailQuery->update(["data->status" => "finished"]);

			$token = auth('api')->claims(["two_fa_mail_status" => "1"])->refresh();

			return response(new AuthResource($token));
		} catch (\Exception	$e) {
			return response($this->sendMail->fails($e->getMessage()));
		}
	}
}
