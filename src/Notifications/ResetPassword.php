<?php

namespace Hasanablak\JwtAuth\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class ResetPassword extends Notification implements ShouldQueue
{
	use Queueable;
	public $sendSms;
	public $randomCode;
	public $channel;

	public function __construct($channel)
	{
		$this->channel = $channel;
		$this->randomCode = rand(10000, 99999);
	}

	public function via($notifiable)
	{
		return [
			$this->channel,
			'database'
		];
	}
	public function toMail($notifiable)
	{
		return (new MailMessage)
			->subject(__('jwt-auth.reset-password-email.subject'))
			->greeting(__('jwt-auth.reset-password-email.greeting'))
			->greeting(__('jwt-auth.dear') . ' ' . $notifiable->name . ' ' . $notifiable->surname . ', ')
			->line(__('jwt-auth.reset-password-email.line1'))
			->line(__('jwt-auth.reset-password-email.line2'))
			->line(new HtmlString('<h1><strong><center>' . $this->randomCode . '</center></strong></h1>'))
			->line(env('APP_NAME') . ' ' . __('jwt-auth.reset-password-email.line4'));
	}
	public function toSms($notifiable)
	{
		return (object) [
			"message"	=>
			__("jwt-auth.dear")
				. " "
				. $notifiable->name
				. " " .
				$notifiable->surname
				. " " .
				__("jwt-auth.reset-password.gsm.message")
				. " " .
				$this->randomCode
		];
	}

	public function toArray()
	{
		return [
			"code"	=>	$this->randomCode,
			"status"	=>	"waiting"
		];
	}
}
