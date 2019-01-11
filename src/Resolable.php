<?php

namespace Bavix\Router;

trait Resolable
{

    /**
     * @var array
     */
    protected $resolver;

    /**
     * @return \Generator
     */
    public function resolver(): \Generator
    {
        return (new Loader($this->resolver, $this))->routes();
    }

}
