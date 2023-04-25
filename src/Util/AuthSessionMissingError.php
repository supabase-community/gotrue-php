<?php

namespace Supabase\Util;

class AuthSessionMissingError extends GoTrueError
{
	public function __construct()
	{
		parent::__construct('Auth session missing!', 'AuthSessionMissingError', 400);
	}
}
