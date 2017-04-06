<?php

namespace Deimos\Router\Type;

use Deimos\Router\Route;
use Deimos\Router\Type;

class Pattern extends Type
{

    /**
     * @var array
     */
    protected $types = [];

    public function build()
    {
        $this->slice['http.scheme'] = $this->scheme;
        $this->slice['http.domain'] = $this->domain;
        $this->slice['path']        = $this->path;
        $this->slice['regex']       = $this->regex;

        if (!isset($this->slice['defaults']))
        {
            $this->slice['defaults'] = [];
        }

        $this->slice['defaults'] += $this->defaults;

        if (!isset($this->slice['methods']))
        {
            $this->slice['methods'] = [];
        }

        $this->slice['methods'] += $this->methods;

        return [
            $this->key => new Route($this->slice)
        ];
    }

}
