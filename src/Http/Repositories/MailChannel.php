<?php

namespace Hasanablak\JwtAuth\Http\Repositories;

use Hasanablak\JwtAuth\Http\Interfaces\IForSendMail;
use	Hasanablak\JwtAuth\Models\LogSendOut;
use	Illuminate\Support\Facades\Mail;
use Hasanablak\JwtAuth\Notifications\RegistrationEmailVerification;

class MailChannel
{

	public function	send($notifiable, RegistrationEmailVerification $notification)
	{
		Mail::send(
			$notification->toMail()->view,
			$notification->toMail(),
			function ($message) use ($notifiable, $notification) {
				$message->to($notifiable->email, $notification->toMail()->title)
					->subject($notification->toMail()->subject);
			}
		);
	}
}
