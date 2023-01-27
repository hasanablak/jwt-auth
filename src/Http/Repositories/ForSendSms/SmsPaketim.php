<?php

namespace Hasanablak\JwtAuth\Http\Repositories\ForSendSms;

use Hasanablak\JwtAuth\Http\Interfaces\IForSendSms;
use	Hasanablak\JwtAuth\Models\LogSendOut;

class SmsPaketim implements IForSendSms
{
	public $type = 0;

	public $channel	= 'api/mesaj_gonder';

	public function	send($data)
	{
		$sms_user 	= 'xxx';
		$sms_pass 	= 'xxx';
		$sms_title 	= 'xxx';



		$gonder = '<Message><Mesgbody>'
			. $data['message']
			. ' </Mesgbody><Number>'
			. $data["number"]
			. '</Number></Message>';

		$xml = '
		<MultiTextSMS>
		<UserName>' . $sms_user . '</UserName>
		<PassWord>' . $sms_pass . '</PassWord>
		<Action>11</Action>
		<Messages>' . $gonder . '</Messages>
		<Originator>' . $sms_title . '</Originator>
		<SDate></SDate>
		</MultiTextSMS>';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://www.smspaketim.com.tr/api/mesaj_gonder');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "data=" . $xml);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$result = curl_exec($ch);


		if (str_contains($result, 'ID')) {
			return (object)	[
				"status"			=>	"success",
				"log_send_out_id"	=>	$this->log($data)->id,
				"body"				=>	$result
			];
		} else {
			return (object)	[
				"status"	=>	"error",
				"message" => $result
			];
		}
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

	function clean($gsm)
	{
		$targetChange = [
			"(",
			")",
			" ",
			"-"
		];
		$changeWith = [
			"",
			"",
			"",
			""
		];
		// baştaki 9 'u silmek için
		//$gsm = substr($gsm, 1, strlen($gsm));

		return str_replace($targetChange, $changeWith, $gsm);
	}
}
