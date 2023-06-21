<?php

namespace Supabase\Util;

class AuthRetryableFetchError extends CustomAuthError
{
    public function __construct($message, $status)
    {
        super($message, 'AuthRetryableFetchError', $status);
    }

    function isAuthApiError($error)
    {
        return isAuthError($error) && $error->name === 'AuthApiError';
    }

    function isAuthError($error)
    {
        return $error != null && is_array($error) && isset($error['__isAuthError']);
    }
}