<?php

namespace Bavix\Router\Type;

use Bavix\Router\Route;
use Bavix\Router\Type;

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
    protected function merge(string $key, array $storage): void
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
    public function build(): array
    {
        $this->slice['http.protocol'] = $this->protocol;
        $this->slice['http.host']     = $this->host;
        $this->slice['path']          = $this->path;
        $this->slice['regex']         = $this->regex;

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
