<?php

namespace Hasanablak\JwtAuth\Providers;

use Hasanablak\JwtAuth\Channels\Database;
use Hasanablak\JwtAuth\Http\Interfaces\IForSendSms;
use Hasanablak\JwtAuth\Http\Interfaces\IForSendMail;
use Hasanablak\JwtAuth\Http\Repositories\ForSendSms\Whatsapp;
use Hasanablak\JwtAuth\Http\Repositories\ForSendSms\WhatsappNew;
use Hasanablak\JwtAuth\Http\Repositories\ForSendSms\SmsPaketim;
use Hasanablak\JwtAuth\Http\Repositories\ForSendMail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;

class JwtAuthServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
		$this->loadViewsFrom(__DIR__ . '/../../resources/views', 'jwt-auth');
		$this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

		/*
		$this->app->singleton(IForSendMail::class, function ($app) {
			return new ForSendMail;
		});
		*/
		//$this->app->singleton(IForSendSms::class, Whatsapp::class);



		Notification::extend('sms', function ($app) {
			return new WhatsappNew();
		});
		/*
			database olarak genişlettiğimizde NotificationSender.php'deki 102. satır yüzünden
			es geçiliyor.
			o yüzden _database yapmak zorunda kalıyoruz.
		*/
		Notification::extend('_database', function ($app) {
			return new Database();
		});

		$this->publishes([
			__DIR__ . '/../../resources/views/' => resource_path('views/vendor/jwt-auth'),
		], 'jwt-auth-views');

		$this->publishes([
			__DIR__ . '/../../lang/' => lang_path()
		], 'jwt-auth-lang');


		// $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'test-package');
		/*
		$this->publishes([
			__DIR__ . '/../../config.php' => config_path('test-package.php'),
		], 'test-package-config');
		*/
	}

	public function register()
	{
		app()->config["filesystems.disks.avatar"] = [
			'driver' => 'local',
			'root' => storage_path('app/public/avatar'),
			'url' => env('APP_URL') . '/storage/avatar',
			'visibility' => 'public',
			'throw' => false,
		];

		/*
		$this->app->register(TestPackageEventServiceProvider::class);
		$this->mergeConfigFrom(__DIR__ . '/../../config.php', 'test-package');
		*/
	}
}
