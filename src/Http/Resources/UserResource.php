<?php

namespace Hasanablak\JwtAuth\Http\Resources;

use Hasanablak\JwtAuth\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
	public $allSettings = false;
	public $model = 'auth';

	public function __construct($allSettings = false, User $model)
	{
		$this->model = $model;
		$this->allSettings = $allSettings;

		parent::__construct(false);
	}
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		return [
			...$this->model->toArray(),
			"settings"	=> $this->allSettings ? $this->model->settingsAll : $this->model->settings
		];
	}
}
