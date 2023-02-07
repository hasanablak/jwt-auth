<?php

namespace Hasanablak\JwtAuth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class YourPasswordHasBeedChanged extends Notification implements ShouldQueue
{
	use Queueable;

	public string $browser;
	public string $ipAddress;
	public string $channel;
	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($channel, $browser, $ip)
	{
		$this->channel = $channel;
		$this->browser = $browser;
		$this->ipAddress = $ip;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return [$this->channel];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable)
	{
		return (new MailMessage)
			->subject(__('jwt-auth.reset-password.your-password-has-been-changed.subject'))
			->greeting(__('jwt-auth.reset-password.your-password-has-been-changed.greeting'))
			->line(__('jwt-auth.dear') . ' ' . $notifiable->name . ' ' . $notifiable->surname . ',')
			->line(__('jwt-auth.reset-password.your-password-has-been-changed-with-this-ip') . ' ' . $this->ipAddress)
			->line(__('jwt-auth.reset-password.your-password-has-been-changed-with-this-browser') . ' ' . $this->browser);
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */

	public function toSms($notifiable)
	{
		return (object) [
			"message"	=>
			__('jwt-auth.dear') . ' ' . $notifiable->name . ' ' . $notifiable->surname . ','
				. PHP_EOL .
				__('jwt-auth.reset-password.your-password-has-been-changed-with-this-ip') . ' ' . $this->ipAddress
				. PHP_EOL .
				__('jwt-auth.reset-password.your-password-has-been-changed-with-this-browser') . ' ' . $this->browser
		];
	}

	public function toArray($notifiable)
	{
		return [
			//
		];
	}
}
