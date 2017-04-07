<?php

namespace Deimos\Router\Type;

use Deimos\Router\Route;
use Deimos\Router\Type;

class Pattern extends Type
{

    /**
     * @var bool
     */
    protected $pathRequired = true;

    /**
     * @var array
     */
    protected $types = [];

    protected function merge($key, array $storage)
    {
        if (!isset($this->slice[$key]))
        {
            $this->slice[$key] = [];
        }

        $this->slice[$key] += $storage;
    }

    /**
     * @return array
     */
    public function build()
    {
        $this->slice['http.scheme'] = $this->scheme;
        $this->slice['http.domain'] = $this->domain;
        $this->slice['path']        = $this->path;
        $this->slice['regex']       = $this->regex;

        $this->merge('defaults', $this->defaults);
        $this->merge('methods', $this->methods);

        return [
            $this->key => new Route($this->slice)
        ];
    }

}
