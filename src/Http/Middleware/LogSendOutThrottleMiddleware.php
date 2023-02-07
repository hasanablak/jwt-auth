<?php

namespace Hasanablak\JwtAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Hasanablak\JwtAuth\Models\LogSendOut;
use Illuminate\Support\Facades\DB;

class LogSendOutThrottleMiddleware
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
		$logSendOut = LogSendOut::select(
			//'log_mail.*',
			//'log_mail_type.name',
			'*',
			DB::raw('UNIX_TIMESTAMP(log_send_outs.created_at) as created_at_unix')
		)
			//->join('log_mail_type', 'log_mail.typeQid', 'log_mail_type.id')
			->where('user_id',    auth('api')->id())
			->where('type_id', $request->type)
			->latest()
			->first();
		$request["gecenSure"] =  time() - $logSendOut?->created_at_unix ?: 0;

		$rules = [
			'gecenSure' => ['required', 'min:300',  'numeric'],
			'type'      => ['required'],
		];

		$messages = [
			'min'  => (300 - $request["gecenSure"]) . ' ms sonra yeni gönderim yapabilirsiniz.'
		];

		$request->validate($rules, $messages);


		return $next($request);
	}
}
