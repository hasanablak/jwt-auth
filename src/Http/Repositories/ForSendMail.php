<?php

namespace Hasanablak\JwtAuth\Http\Repositories;

use Hasanablak\JwtAuth\Http\Interfaces\IForSendMail;
use	Hasanablak\JwtAuth\Models\LogSendOut;
use	Illuminate\Support\Facades\Mail;

class ForSendMail implements IForSendMail
{
	public $type = 0;

	public function	send($useData, $data)
	{
		Mail::send($useData["view"], $data,	function ($message) use ($useData) {
			$message->to($useData["email"], $useData["title"])
				->subject($useData["subject"]);
		});

		return $this->log(array_merge($useData,	$data));
	}

	public function log(array $logData): LogSendOut
	{
		$log = new LogSendOut;
		$log->user_id = auth('api')->id();
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
