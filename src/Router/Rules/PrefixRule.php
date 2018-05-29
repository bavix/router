<?php

namespace Bavix\Router\Rules;

use Bavix\Router\RegExp;
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

    /**
     * @inheritdoc
     */
    protected function prepare(): void
    {
        $this->protocol = new RegExp('\w+');
        $this->host = new RegExp('[^\/]+');
    }

}
