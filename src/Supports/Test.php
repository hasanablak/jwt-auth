<?php

namespace Hasanablak\JwtAuth\Supports;

use Hasanablak\JwtAuth\Models\UserSetting;

class Test
{
	public function __construct()
	{
		//$data = UserSetting::where('user_id', auth('api')->id())
		$data = UserSetting::where('user_id', 1)
			/*
		->where(function ($q) use ($all) {
			if (!$all) return $q->where('is_hidden', 0);
		})
		*/
			->get();
		/*
		$this->userSettings = $data->mapWithKeys(function ($item) {
			return [$item->key => $item->value == 'true' ? (true) : ($item->value	  == 'false' ? false : $item->value)];
		})->toArray();
		*/
		foreach ($data as $setting) {

			$a = $setting->key;
			$this->$a = $setting->value;
		}
	}



	public function gsm_dial_code2()
	{
		return "test";
	}

	public function __get($field)
	{
		/*
		return $this->userSettings[$field]
			?:  "You can access the '$" . $field . "' field but this field is not defined";
		*/
		return "You can access the '$" . $field . "' field but this field is not defined";;
		//return $this->modifidedField[$field];
	}

	public function all()
	{
		return (object) $this->userSettings;
	}
}
