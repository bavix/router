<?php

namespace Deimos\Router\Exceptions;

class PathNotFound extends \Exception
{
    public function __construct($key = null, $code = 0, \Exception $previous = null)
    {
        $message = 'Path \'' . $key . '\' not found';

        parent::__construct($message, $code, $previous);
    }
}