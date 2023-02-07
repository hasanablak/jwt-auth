<?php

namespace Hasanablak\JwtAuth\Http\Interfaces;

use Illuminate\Notifications\Notification;
use Hasanablak\JwtAuth\Models\LogSendOut;
use Hasanablak\JwtAuth\Notifications\RegistrationGsmVerification;

interface IForSendSmsNew
{
	public function send($notifiable, RegistrationGsmVerification $notification);

	public function log(mixed $logData = []): LogSendOut;

	public function fails($message	= "");

	public function success($message = "", $data = []);
}
