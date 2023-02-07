<?php

namespace Hasanablak\JwtAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LocalizationMiddleware
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

		$local = $request->header('X-localization', 'tr');
		app()->setLocale($local);
		return $next($request);
	}
}
