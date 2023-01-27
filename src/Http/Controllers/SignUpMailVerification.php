<?php

namespace Hasanablak\JwtAuth\Http\Controllers;

use App\Http\Controllers\Controller;
use Hasanablak\JwtAuth\Http\Interfaces\IForSendMail;
use Illuminate\Http\Request;
use	Hasanablak\JwtAuth\Models\LogSendOut;
use Hasanablak\JwtAuth\Http\Resources\AuthResource;

class SignUpMailVerification extends Controller
{
	public $sendMail;

	public function __construct(IForSendMail $sendMail)
	{
		$this->sendMail = $sendMail;
		$this->sendMail->type = '4';
	}


	public function codeSend(Request $request)
	{
		try {

			$request->validate([
				"type" => "required|in:" . $this->sendMail->type
			]);

			$useData["view"]		= 'jwt-auth::emails.registration-verification';
			$useData["email"]		= auth('api')->user()->email;
			$useData["title"]		= 'Registration Verification';
			$useData["subject"]		= 'Registration Verification';

			$data["randomCode"]		= rand(100000, 999999);
			$data["status"]			= "waiting";
			$data["email"]			= auth('api')->user()->email;

			$logData	= $this->sendMail->send($useData,	$data);

			return response($this->sendMail->success("ok", ["log_send_out_id" => $logData->id]));
		} catch (\Exception	$e) {
			return response($this->sendMail->fails($e->getMessage()));
		}
	}

	public function codeConfirm(Request	$request)
	{
		try {
			$request->validate([
				"log_send_out_id"	=>	"required",
				"code"				=>	"required",
				"type" 				=> "required|in:" . $this->sendMail->type
			]);
			$log_mailQuery = LogSendOut::where('user_id', auth('api')->id())
				->where('type_id', $this->sendMail->type)
				->where('id', $request->log_send_out_id)
				->whereJsonContains('data->status',	'waiting')
				->whereJsonContains('data->randomCode',	intval($request->code));

			$log_mailQuery->firstOrFail();

			$log_mailQuery->update(["data->status" => "finished"]);

			auth('api')->user()->email_verified_at = date('Y-m-d H:i:s');
			auth('api')->user()->save();
			//$UsersTokenQuery = UsersToken::where('token', $request->header('Authorization'));
			//$UsersTokenQuery->update(["two_fa_mail_verify_status" => 1]);

			//$user =				auth('api')->user();
			$token = auth('api')->claims(["mail_verify_status" => "1"])->refresh();
			return response(new AuthResource($token));
		} catch (\Exception	$e) {
			return response($this->sendMail->fails($e->getMessage()));
		}
	}
}
