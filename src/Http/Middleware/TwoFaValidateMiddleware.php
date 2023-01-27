<?php

namespace Hasanablak\JwtAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TwoFaValidateMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
	 * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
	 */
	public function handle(Request $request, Closure $next)
	{
		$jwt = auth('api')->payload()->toArray();
		$validator = Validator::make(
			$jwt,
			[
				'mail_verify_status'	=>	"in:1",
				'two_fa_mail_settings'	=>	"in:" . $jwt["two_fa_mail_status"],
				'two_fa_gsm_settings'	=>	"in:" . $jwt["two_fa_gsm_status"]
			],
			[
				"mail_verify_status.in"		=>	"Need email verify.",
				"two_fa_mail_settings.in"	=>	"Two factor authentication failed. Mail",
				"two_fa_gsm_settings.in"	=>	"Two factor authentication failed. Gsm"
			]
		);

		if ($validator->fails()) {
			return response([
				"message"	=>	$validator->errors()->first(),
				"errors"	=>	$validator->errors()
			], 401);
		}

		return $next($request);
	}
}
