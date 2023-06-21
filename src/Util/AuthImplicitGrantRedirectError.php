<?php

namespace Supabase\Util;

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