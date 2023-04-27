<?php

namespace Supabase\Util;

class GoTrueUnknownError extends GoTrueError
{
	private string $name = 'GoTrueUnknownError';
	private mixed $originalError = null;

	public function __construct($message, $originalError)
	{
		parent::__construct($message);
		$this->originalError = $originalError;
	}
}
