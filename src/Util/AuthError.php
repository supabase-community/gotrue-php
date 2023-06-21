<?php

namespace Supabase\Util;

class AuthError extends Error
{
    protected $isAuthError = true;

    public function __construct($message)
    {
        super($message);
        $this->name = 'AuthError';
    }
}