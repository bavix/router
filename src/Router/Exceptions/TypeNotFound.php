<?php

namespace Deimos\Router\Exceptions;

class TypeNotFound extends \Exception
{
    public function __construct($key = null, $code = 0, \Exception $previous = null)
    {
        $message = 'Type \'' . $key . '\' not found';

        parent::__construct($message, $code, $previous);
    }
}