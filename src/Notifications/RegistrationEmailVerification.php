<?php

namespace Hasanablak\JwtAuth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;


class RegistrationEmailVerification extends Notification implements ShouldQueue
{
	use Queueable;

	public $notifiable;
	public $mailData;

	public function __construct()
	{
		$randCode = rand(10000, 99999);

		$this->mailData = (object) [
			"view"		=> 'jwt-auth::emails.change-email.send-code-to-current-email',
			"email"		=> auth('api')->user()->email,
			"title"		=> 'Code for change your email address',
			"subject"	=> 'Code for change your email address',
			"code"		=> $randCode,
			"status"	=> "waiting"
		];
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		$this->notifiable = $notifiable;

		return ['mail', 'database'];
	}

	public function toMail()
	{
		return (new MailMessage)
			->subject(__('jwt-auth.registration.email.verification.subject'))
			->greeting(__('jwt-auth.registration.email.verification.greeting'))
			->line(__('jwt-auth.registration.email.verification.line1'))
			->line(__('jwt-auth.registration.email.verification.line2'))
			->line(new HtmlString('<h1><center>' . $this->mailData->code . '</center></h1>'))
			->line(env('APP_NAME') . ' ' . __('jwt-auth.registration.email.verification.line4'));
	}

	public function toArray()
	{
		return $this->mailData;
	}
}
