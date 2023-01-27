<?php

namespace Hasanablak\JwtAuth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotifications;

class ResetPassword extends ResetPasswordNotifications
{
	use Queueable;

	public function resetUrl($notifiable)
	{
		return env(
			'FRONTEND_HOST',
			'Lütfen .env dosyanızın içerisine `FRONTEND_HOST` adında bir alan tanımlayıp fronend adresinizi yazınız.'
		) .
			'/reset-password'
			. '?token=' . $this->token
			. '&email=' . $notifiable->getEmailForPasswordReset();
	}
}
