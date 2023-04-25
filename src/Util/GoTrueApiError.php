<?php

namespace Supabase\Util;

class GoTrueApiError extends GoTrueError
{
	//protected string $code;
	protected string $name;
	public $details;
	public $hint;
	public $response;

	public function __construct($code, $details, $hint, $message, $response)
	{
		parent::__construct($code, $details, $hint, $message, $response);
		$this->code = $code;
		$this->details = $details;
		$this->hint = $hint;
		$this->message = $message;
		$this->response = $response;
		$this->name = 'PostgrestApiError';
	}
}
