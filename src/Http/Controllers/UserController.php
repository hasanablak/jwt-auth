<?php

namespace Hasanablak\JwtAuth\Http\Controllers;

use Illuminate\Http\Request;
use Hasanablak\JwtAuth\Models\User;

class UserController extends Controller
{

	public function show($username)
	{

		$user = User::where('username', $username)->firstOrFail();

		return response([
			"status"	=>	"success",
			"data"		=>	[
				...$user->toArray(),
				"settings"	=>	$user->settings
			]
		]);
	}
}
