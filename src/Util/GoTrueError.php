<?php

namespace Supabase\Util;

class PostgrestError extends \Exception
{
    protected bool $isPostgrestError = true;

    public function __construct($message)
    {
        parent::__construct($message);
        $this->name = 'PostgrestError';
    }

    public static function isPostgrestError($e)
    {
        return $e != null && isset($e->isPostgrestError);
    }
}
