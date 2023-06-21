<?php

namespace Supabase\Util;

class AuthInvalidCredentialsError extends CustomAuthError
{
    public function __construct($message)
    {
        super($message, 'AuthInvalidCredentialsError', 400);
    }
}