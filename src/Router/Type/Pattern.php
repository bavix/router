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
     * @param string $key
     * @param array  $storage
     */
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

        if (empty($this->slice['methods']))
        {
            $this->slice['methods'] = $this->methods;
        }

        $this->merge('defaults', $this->defaults);

        return [
            $this->key => new Route($this->slice)
        ];
    }

}
