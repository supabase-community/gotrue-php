<?php

namespace Supabase\Util;

class GoTrueUnknownError extends GoTrueError
{
    public function __construct($message, $originalError)
    {
        parent::__construct($message);
        $this->name = 'GoTrueUnknownError';
        $this->originalError = $originalError;
    }
}
