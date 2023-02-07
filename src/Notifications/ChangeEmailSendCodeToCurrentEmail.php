<?php

namespace Hasanablak\JwtAuth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;


class ChangeEmailSendCodeToCurrentEmail extends Notification implements ShouldQueue
{
	use Queueable;

	public $notifiable;
	public $mailData;

	public function __construct()
	{
		$randCode = rand(10000, 99999);

		$this->mailData = (object) [
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
			->subject(__('jwt-auth.change-email.subject'))
			->greeting(__('jwt-auth.change-email.greeting'))
			->line(__('jwt-auth.change-email.current.line1'))
			->line(__('jwt-auth.change-email.line2'))
			->line(new HtmlString('<h1><center>' . $this->mailData->code . '</center></h1>'))
			->salutation(env('APP_NAME') . ' ' . __('jwt-auth.have-a-good-day'));
	}

	public function toArray()
	{
		return $this->mailData;
	}
}
