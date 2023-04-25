<?php

class AuthApiError extends AuthError
{
	public function __construct($message, $status)
	{
		super(message);
		$this->name = 'AuthApiError';
		$this->status = $status;
	}

	public function toArray()
	{
		return [
			'name'    => $this->name,
			'message' => $this->message,
			'status'  => $this->status,
		];
	}
}

class AuthUnknownError extends AuthError
{
	public function __construct($message, $originalError)
	{
		super(message);
		$this->name = 'AuthUnknownError';
		$this->originalError = $thisoriginalError;
	}
}

class AuthError extends Error
{
	protected $isAuthError = true;

	public function __construct($message)
	{
		super($message);
		$this->name = 'AuthError';
	}
}

class CustomAuthError extends AuthError
{
	public function __construct($message, $name, $status)
	{
		super($message);
		$this->name = $name;
		$this->status = $status;
	}

	public function toArray()
	{
		return [
			'name'    => $this->name,
			'message' => $this->message,
			'status'  => $this->status,
		];
	}
}

class AuthSessionMissingError extends CustomAuthError
{
	public function __construct()
	{
		super('Auth session missing!', 'AuthSessionMissingError', 400);
	}
}

class AuthInvalidCredentialsError extends CustomAuthError
{
	public function __construct($message)
	{
		super($message, 'AuthInvalidCredentialsError', 400);
	}
}

class AuthImplicitGrantRedirectError extends CustomAuthError
{
	public function __construct($message, $details)
	{
		super('Implicit grant redirect', 'AuthImplicitGrantRedirectError', 302);
		$this->details = $details;
	}

	public function toArray()
	{
		return [
			'name'    => $this->name,
			'message' => $this->message,
			'status'  => $this->status,
			'details' => $this->details,
		];
	}
}

class AuthRetryableFetchError extends CustomAuthError
{
	public function __construct($message, $status)
	{
		super($message, 'AuthRetryableFetchError', $status);
	}
}

function isAuthApiError($error)
{
	return isAuthError($error) && $error->name === 'AuthApiError';
}

function isAuthError($error)
{
	return $error != null && is_array($error) && isset($error['__isAuthError']);
}
