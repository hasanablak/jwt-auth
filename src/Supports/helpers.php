<?php

use Hasanablak\JwtAuth\Models\UserSetting;

function settingsMap($id = null, $all = false)
{
	$id = is_null($id) || empty($id) ? auth('api')->id() : $id;

	$data = UserSetting::where('user_id', $id)->where(function ($q) use ($all) {
		if (!$all) return $q->where('is_hidden', 0);
	})->get();
	$data = $data->mapWithKeys(function ($item) {
		return [$item->key => $item->value == 'true' ? (true) : ($item->value	  == 'false' ? false : $item->value)];
	});
	return (object) $data->toArray();
}
