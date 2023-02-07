<?php

namespace Hasanablak\JwtAuth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;


class YourEmailAddressWasChanged extends Notification implements ShouldQueue
{
	use Queueable;

	public $notifiable;
	public $mailData;
	public $newEmailAddress = [];

	public function __construct($newEmailAddress)
	{
		$this->mailData["newEmailAddress"] = $newEmailAddress;
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

		$this->mailData["oldMailAddress"] = $notifiable->email;

		return ['mail', 'database'];
	}

	public function toMail()
	{
		return (new MailMessage)
			->subject(env('APP_NAME') . ' ' . __('jwt-auth.change-email.your-email-adress-was-changes'))
			->greeting(__('jwt-auth.change-email.your-email-adress-was-changes'))
			->line(__('jwt-auth.change-email.new-email.address') . ' ' . $this->mailData["newEmailAddress"])
			->salutation(env('APP_NAME') . ' ' . __('jwt-auth.have-a-good-day'));
	}

	public function toArray()
	{
		return $this->mailData;
	}
}
