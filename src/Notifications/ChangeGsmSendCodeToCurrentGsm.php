<?php

namespace Hasanablak\JwtAuth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class ChangeGsmSendCodeToCurrentGsm extends Notification implements ShouldQueue
{
	use Queueable;

	public $notifiable;
	public $smsData;

	public function __construct()
	{
		$randCode = rand(10000, 99999);
		$this->smsData = [
			"message"	=> __('jwt-auth.change-gsm.current.message') . $randCode,
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

		return ['sms', 'database'];
	}

	public function toSms()
	{
		return (object) $this->smsData;
	}


	public function toArray()
	{
		return $this->smsData;
	}
}
