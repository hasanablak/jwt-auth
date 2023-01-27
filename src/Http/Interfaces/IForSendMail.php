<?php

namespace Hasanablak\JwtAuth\Http\Interfaces;

use Hasanablak\JwtAuth\Models\LogSendOut;

interface IForSendMail
{
	public function send(array $useData, array $logData);

	public function log(array $logData): LogSendOut;

	public function fails($message	= "");

	public function success($message = "", $data = []);
}
