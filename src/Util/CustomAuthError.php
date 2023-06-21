<?php

namespace Supabase\Util;

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