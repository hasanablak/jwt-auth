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
	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($browser, $ipAddress)
	{
		$this->browser = $browser;
		$this->ipAddress = $ipAddress;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return ['mail'];
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
			->line('Dear ' . $notifiable->name . ' ' . $notifiable->surname)
			->line('Your password has been changed from the IP address: ' . $this->ipAddress)
			->line($this->browser);
		#->action('Notification Action', url('/'))
		#->line('Thank you for using our application!');
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function toArray($notifiable)
	{
		return [
			//
		];
	}
}
