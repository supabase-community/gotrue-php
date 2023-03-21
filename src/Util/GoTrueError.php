<?php

namespace Supabase\Util;

class GoTrueError extends \Exception
{
    protected bool $isGoTrueError = true;

    public function __construct($message)
    {
        parent::__construct($message);
        $this->name = 'GoTrueError';
    }

    public static function isGoTrueError($e)
    {
        return $e != null && isset($e->isGoTrueError);
    }
}
