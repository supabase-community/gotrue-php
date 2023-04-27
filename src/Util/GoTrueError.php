<?php

namespace Supabase\Util;

class GoTrueError extends \Exception
{
	protected bool $isGoTrueError = true;
	private string $name = 'GoTrueError';

	public function __construct($message)
	{
		parent::__construct($message);
	}

	public static function isGoTrueError($e)
	{
		return $e != null && isset($e->isGoTrueError);
	}
}
