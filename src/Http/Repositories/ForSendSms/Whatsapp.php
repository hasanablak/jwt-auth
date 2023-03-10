<?php

namespace Hasanablak\JwtAuth\Http\Repositories\ForSendSms;

use Hasanablak\JwtAuth\Http\Interfaces\IForSendSms;
use	Hasanablak\JwtAuth\Models\LogSendOut;
use	Illuminate\Support\Facades\Http;


class Whatsapp implements IForSendSms
{

	public function __construct()
	{

		//dd(debug_backtrace()[1], "Whatsapp.php");
		//dd("Repositoryies Whatsapp.php forsendSms dir");
	}
	public $type = 1;

	public $channel	= 'send-whatsapp-message';

	public function	send($data)
	{
		$data["gsm"] = "905510898465";
		$data["message"] = "Merhaba";

		$arr = [
			'number' => $data["gsm"],
			'message' => $data["message"],
		];

		$response =	Http::baseUrl(env('GSM_SEND_HOST'))
			->post($this->channel, $arr);


		$res = json_decode($response->body());
		if (isset($res->status)	&& $res->status	== 'success') {
			return (object)	[
				"status"			=>	"success",
				"log_send_out_id"	=>	$this->log($data)->id,
				"body"				=>	$response->body()
			];
		} else {
			return (object)	[
				"status"	=>	"error",
				"message" => isset($res->message) ?	$res->message :	''
			];
		}
	}

	public function log(mixed $logData = []): LogSendOut
	{
		$log = new LogSendOut;
		$log->user_id = auth('api')->id() ?: '2';
		$log->type_id = $this->type;
		$log->data = json_encode($logData);
		$log->save();

		return $log;
	}

	public function fails($message = "")
	{
		return [
			"status"	=> "error",
			"message"	=> $message
		];
	}

	public function success($message = "", $data = [])
	{
		return [
			"status"	=> "success",
			"message"	=> $message,
			"data"		=> $data
		];
	}
}
