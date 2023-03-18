<?php

namespace Supabase\Util;

class PostgrestUnknownError extends PostgrestError
{
    public function __construct($message, $originalError)
    {
        parent::__construct($message);
        $this->name = 'PostgrestUnknownError';
        $this->originalError = $originalError;
    }
}
