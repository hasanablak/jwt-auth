<?php

namespace Hasanablak\JwtAuth\Http\Repositories\ForSendSms;

use	Hasanablak\JwtAuth\Models\LogSendOut;
use Illuminate\Notifications\Notification;
use	Illuminate\Support\Facades\Http;
use ReflectionClass;
use Illuminate\Notifications\AnonymousNotifiable;


class WhatsappNew
{

	public $channel	= 'send-whatsapp-message';

	public function send($notifiable, Notification $notification)
	{
		if ((new ReflectionClass(AnonymousNotifiable::class))->getName() == get_class($notifiable)) {
			$gsm = $notifiable?->routes["gsm"];
		} else {
			$gsm = $notifiable?->gsm;
		}

		$arr = [
			'number' =>  $gsm,
			'message' => $notification->toSms($notifiable)->message,
		];

		$response =	Http::baseUrl(env('GSM_SEND_HOST'))
			->post($this->channel, $arr);

		//$res = json_decode($response->body());
		/*
		if (isset($res->status)	&& $res->status	== 'success') {

			$logSendOut = $this->log([
				"user"	=>	$notifiable,
				"type"	=>	$notification->type
			]);
			return (object)	[
				"status"			=>	"success",
				"log_send_out_id"	=>	$this->log($res)->id,
				"body"				=>	$response->body()
			];
		} else {
			return (object)	[
				"status"	=>	"error",
				"message" => isset($res->message) ?	$res->message :	''
			];
		}
		*/
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
