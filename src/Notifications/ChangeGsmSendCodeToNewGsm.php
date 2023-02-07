<?php

namespace Hasanablak\JwtAuth\Notifications;

use Hasanablak\JwtAuth\Channels\Database;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class ChangeGsmSendCodeToNewGsm extends Notification implements ShouldQueue
{
	use Queueable;

	public $notifiable;
	public $smsData;

	public function __construct()
	{
		$randCode = rand(10000, 99999);
		$this->smsData = [
			"message"	=> __('jwt-auth.change-gsm.new.message') . $randCode,
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
		$this->smsData["newGsm"] = $notifiable->routes["gsm"];
		return ['_database', 'sms'];
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
