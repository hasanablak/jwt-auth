<?php

namespace Hasanablak\JwtAuth\Channels;

use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Hasanablak\JwtAuth\Models\User;
use Illuminate\Notifications\AnonymousNotifiable;

class Database extends DatabaseChannel
{
	/**
	 * Notification::extends ile database'i tekrar genişletsem bile
	 * NotificationSender.php:102. satırdaki if bizi engeller
	 * çünkü yine Notification::route'u kullanacağım
	 * ve yine bu bir AnonymousNotifiable üretecek
	 * ve yine databae channel'ını kullanırsam 102. satırdaki if'i geçemeyeceğim
	 * 
	 * ancak genişletme adını database değil de database_ yaparsam
	 * 
	 * bu sefer buradan kurtuluruz aslında.
	 * 
	 * Daha sonra 145. satırda driverımı ayağa kaldırır ve send methodu çalışır?
	 */
	public function send($notifiable, Notification $notification)
	{
		if ($notifiable instanceof AnonymousNotifiable) {
			return User::find(auth('api')->id())
				->morphMany(DatabaseNotification::class, 'notifiable')->create(
					$this->buildPayload($notifiable, $notification)
				);
		} else {
			return parent::send($notifiable, $notification);
		}


		dump('Database.php içerisine gelen notifiable değişkeninin değeri');
		dump('Eğer bir "Model" geliyor ise; var ise modelin içerisindeki routeNorificationFor methodunu
eğer yok ise Trait üzerinden geleni kullanır.');
		dump('Eğer bir "AnonymousNotifiable" ise routeNotifiacationFor methodunu kullanır');
		dump("routeNotifiacationFor içerisindekileri hala anlamadım.");
		dump(get_class($notifiable));


		dd("?");
	}
}
