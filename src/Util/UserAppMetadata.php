<?php

class UserAppMetadata
{
	public string $provider;
	public array $key;

	public function __construct($data)
	{
		$this->provider = $data->provider;
		$this->key = $data->key;
	}
}
