<?php

namespace Hasanablak\JwtAuth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class YourGsmWasChanged extends Notification implements ShouldQueue
{
	use Queueable;

	public $notifiable;
	public $smsData = [];

	public function __construct($newGsm)
	{
		$this->smsData["newGsm"] = $newGsm;
		$this->smsData["message"] = __('jwt-auth.change-gsm.your-phone-number-was-changed') . PHP_EOL . __('jwt-auth.change-gsm.new-phone-number') . ' ' . $newGsm;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		$this->smsData["oldGsm"] = $notifiable->gsm;

		return ['database', 'sms'];
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
