<?php

namespace Hasanablak\JwtAuth\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
	public $token;
	public $decodedToken;

	public function __construct($token = null)
	{
		$this->token = $token;
		$this->decodedToken = $token ? json_decode(base64_decode(explode(".", $this->token)[1])) : "";
	}

	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		if (!$this->token) {
			return [
				'status' => 'error',
				'message' => 'Unauthorized',
			];
		}
		$returnArray = [
			"status"	=>	"success",
			"token"		=>	'Bearer ' . $this->token
		];
		if (
			$this->decodedToken->two_fa_gsm_settings == $this->decodedToken->two_fa_gsm_status
			&& $this->decodedToken->two_fa_mail_settings == $this->decodedToken->two_fa_mail_status
			&& ($this->decodedToken->mail_verify_status == "1" || $this->decodedToken->gsm_verify_status == "1")
		) {
			$returnArray["user"] = auth('api')->user();
			$returnArray["user"]["settings"] = auth('api')->user()->settingsAll;
		}
		return $returnArray;
	}
}
