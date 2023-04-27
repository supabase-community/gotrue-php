<?php

class UserMetadata
{
	public array $key;

	public function __construct($data)
	{
		$this->key = $data->key;
	}
}
