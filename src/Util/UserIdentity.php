<?php

class UserIdentity
{
	public string $id;
	public string $user_id;
	public array $identity;
	public string $provider;
	public string $created_at;
	public string $last_sign_in_at;
	public string $updated_at;

	public function __construct($data)
	{
		$this->id = $data->id;
		$this->user_id = $data->user_id;
		$this->identity = $data->identity;
		$this->provider = $data->provider;
		$this->created_at = $data->created_at;
		$this->last_sign_in_at = $data->last_sign_in_at;
		$this->updated_at = $data->updated_at;
	}
}
