<?php

namespace Bavix\Router\Rules;

use Bavix\Router\Resolable;
use Bavix\Router\Rule;

class PrefixRule extends Rule
{

    use Resolable;

    /**
     * @var string
     */
    protected $protocol;

    /**
     * @var string
     */
    protected $host;

}
