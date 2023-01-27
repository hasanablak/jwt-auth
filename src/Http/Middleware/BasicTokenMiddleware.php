<?php

namespace Hasanablak\JwtAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BasicTokenMiddleware
{
	/**
	 * @param  \Illuminate\Http\Request	 $request
	 * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
	 * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
	 */
	public function handle(Request $request, Closure $next)
	{
		if ($request->header('Authorization') == null) {
			return response([
				"status"	=> "error",
				"message"	=> "You dont have a token on header (Authorization)"
			], 402);
		}
		try {
			auth('api')->payload(); // çözümlenebiliyor mu?

			return $next($request);
		} catch (\Exception $e) {
			return response([
				"status"	=> "error",
				"message"	=> $e->getMessage()
			], 403);
		}
	}
}
