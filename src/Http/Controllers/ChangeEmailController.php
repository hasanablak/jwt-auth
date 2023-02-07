<?php


namespace Hasanablak\JwtAuth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Hasanablak\JwtAuth\Models\Notification;
use Hasanablak\JwtAuth\Notifications\ChangeEmailSendCodeToCurrentEmail;
use Hasanablak\JwtAuth\Notifications\ChangeEmailSendCodeToNewEmail;
use Hasanablak\JwtAuth\Notifications\YourEmailAddressWasChanged;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use ReflectionClass;

class ChangeEmailController extends Controller
{

	public $notificationTypes =  [
		'new' => '',
		'current' => ''
	];

	public function __construct()
	{
		$this->notificationTypes	= [
			"new"	=> (new ReflectionClass(ChangeEmailSendCodeToNewEmail::class))->getName(),
			"current"	=> (new ReflectionClass(ChangeEmailSendCodeToCurrentEmail::class))->getName()
		];
	}

	public function sendCodeToCurrentEmail()
	{
		if (is_null(auth('api')->user()->email)) return response(["status" => "error"]);

		auth('api')->user()->notify(new ChangeEmailSendCodeToCurrentEmail());

		return response([
			"status"	=>	"success"
		]);
	}

	public function confirmCurrentEmailSentCode(Request $request)
	{
		$request->validate([
			"code"			=>	"required"
		]);

		$logSendOutQuery = Notification::control(
			type: $this->notificationTypes["current"],
			status: 'waiting',
			code: $request->code,
			notifiable_id: auth('api')->id()
		);

		$logSendOutQuery->firstOrFail();

		$logSendOutQuery->update(["data->status" => "approved"]);

		return response([
			"status"	=>	"success"
		]);
	}

	public function sendCodeToNewEmail(Request $request)
	{
		$request->validate([
			"currentEmailSendedCode"	=> [Rule::requiredIf(fn () => !is_null(auth('api')->user()->email))],
			"newEmailAddress"		=>	"required|email|unique:users,email"
		]);

		if (!is_null(auth('api')->user()->email)) {
			Notification::control(
				type: $this->notificationTypes["current"],
				status: 'approved',
				code: $request->currentEmailSendedCode,
				notifiable_id: auth('api')->id()
			)->firstOrFail();
		}



		FacadesNotification::route('mail', $request->newEmailAddress)
			->notify(new ChangeEmailSendCodeToNewEmail());

		return response([
			"status"	=>	"success"
		]);
	}

	public function confirmNewEmailSentCode(Request $request)
	{
		$request->validate([
			"code"			=>	"required"
		]);

		$logSendOutQuery = Notification::control(
			type: $this->notificationTypes["new"],
			status: 'waiting',
			code: $request->code,
			notifiable_id: auth('api')->id()
		);



		$logSendOutQuery->firstOrFail();

		$logSendOutQuery->update(["data->status" => "approved"]);

		return response([
			"status"	=>	"success"
		]);
	}

	public function update(Request $request)
	{
		$request->validate([
			"current"	=> [Rule::requiredIf(fn () => !is_null(auth('api')->user()->email))],
			"new"		=>	"required"
		]);
		foreach ($request->only("current", "new") as $index => $sendedCode) {

			$logSendOutQuery = Notification::control(
				type: $this->notificationTypes[$index],
				status: 'approved',
				code: $sendedCode,
				notifiable_id: auth('api')->id()
			);

			$logSendOut = $logSendOutQuery->firstOrFail();

			$logSendOutQuery->update(["data->status" => "finished"]);
		}
		$newEmailAddress = json_decode($logSendOut->data)->newEmailAddress;

		if (!is_null(auth('api')->user()->email)) {
			auth('api')->user()->notify(new YourEmailAddressWasChanged(
				newEmailAddress: $newEmailAddress
			));
		}

		auth('api')->user()->email_verified_at = date('Y-m-d H:i:s');
		auth('api')->user()->email = $newEmailAddress;
		auth('api')->user()->save();

		return response([
			"status"	=>	"success",
			"message"	=>	__('jwt-auth.change-email.your-email-adress-was-changes')
		]);
	}
}
