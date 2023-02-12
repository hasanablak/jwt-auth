<?php

namespace Hasanablak\JwtAuth\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Hash;
use Hasanablak\JwtAuth\Notifications\ResetPassword;
use Illuminate\Support\Str;
use Hasanablak\JwtAuth\Supports\Test;
use Hasanablak\JwtAuth\Http\Interfaces\IForSendSms;
use Illuminate\Support\Facades\Notification;


class User extends Authenticatable implements JWTSubject
{
	use HasApiTokens, HasFactory, Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'name',
		'surname',
		'email',
		'password',
		'gsm',
		'is_admin'
	];
	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
		'gsm_verified_at' => 'datetime',
	];

	public function getJWTIdentifier()
	{
		return $this->getKey();
	}

	public function getJWTCustomClaims()
	{
		return [
			"two_fa_mail_settings" => $this->settingsAll->two_fa_mail ?? '0',
			"two_fa_gsm_settings" => $this->settingsAll->two_fa_gsm ?? '0',
			"two_fa_mail_status" =>	"0",
			"two_fa_gsm_status" =>	"0",
			"mail_verify_status" => !is_null($this->email_verified_at) ? "1" : (is_null($this->email) ? "-1" : "0"),
			"gsm_verify_status" => !is_null($this->gsm_verified_at) ? "1" : (is_null($this->gsm) ? "-1" : "0"),
		];
	}

	protected function settings(): Attribute
	{
		return Attribute::make(
			get: fn ($value, $attributes) => new Test($attributes["id"])
			//get: fn ($value, $attributes) => settingsMap($attributes["id"], false)
		);
	}

	protected function settingsAll(): Attribute
	{
		return Attribute::make(
			get: fn ($value, $attributes) => settingsMap($attributes["id"], true)
		);
	}

	protected function password(): Attribute
	{

		return new Attribute(
			//get: fn($value) => "",
			set: fn ($value) => Hash::make($value)
		);
	}

	protected static function boot()
	{
		parent::boot();

		static::creating(function ($user) {
			$username = Str::slug($user->name . $user->surname) ?: strtolower(Str::random(5));
			$usernameCount = User::where('username', $username)->count();
			//$trade->slug = Str::random(40);

			$user->username = $username = $usernameCount > 0 ? $username . $usernameCount + 1 : $username;
			$user->avatar = '/storage/avatar/default.png';
		});
	}

	public function sendPasswordResetNotification($token)
	{
		$this->notify(new ResetPassword($token));

		//Notification::send($this, new ResetPassword($token));
	}
}
