<?php

namespace Hasanablak\JwtAuth\Supports;

use Hasanablak\JwtAuth\Http\Interfaces\IForSendSms;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class Test2
{

	public $baseUrl = '';

	public $channel = 'send-whatsapp-message';


	public function __construct()
	{
		//dd(debug_backtrace()[1], "Test.php");

		$this->baseUrl = env('NODE_WHATSAPP_HOST');
		dd(debug_backtrace()[1], "Test.php");
	}

	public function send($notifiable, Notification $notification)
	{
		try {
			$arr = $notification?->toSms();

			$response =
				Http::baseUrl($this->baseUrl)
				->post($this->channel, $arr);

			if ($response->status() == "200") {
				return $response->json();
			}
			return [
				"status"	=>	"error",
				"message"	=>	"code ise not 200"
			];
		} catch (\Exception $e) {
			return [
				"status"	=>	"error",
				"message"	=>	"crytic error"
			];
		}
	}
}
