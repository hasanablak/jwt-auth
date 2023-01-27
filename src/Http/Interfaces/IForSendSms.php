<?php

namespace Hasanablak\JwtAuth\Http\Interfaces;

use Hasanablak\JwtAuth\Models\LogSendOut;

interface IForSendSms
{
	public function send(array $data);

	public function log(array $logData): LogSendOut;

	public function fails($message	= "");

	public function success($message = "", $data = []);
}
