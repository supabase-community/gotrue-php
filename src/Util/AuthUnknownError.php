<?php

namespace Supabase\Util;

class AuthUnknownError extends AuthError
{
    public function __construct($message, $originalError)
    {
        super(message);
        $this->name = 'AuthUnknownError';
        $this->originalError = $thisoriginalError;
    }
}