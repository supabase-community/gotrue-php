<?php

namespace Supabase\Util;

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