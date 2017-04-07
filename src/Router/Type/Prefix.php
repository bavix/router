<?php

namespace Deimos\Router\Type;

use Deimos\Slice\Slice;

class Prefix extends Http
{

    protected $pathRequired = true;

    protected function storage($key, Slice $slice)
    {
        list($path, $regex) = $this->path($slice);

        return [
            'defaults' => $this->slice->getData('defaults', []) + $this->defaults,
            'methods'  => $this->slice->getData('methods', []) + $this->methods,
            'scheme'   => $this->scheme,
            'domain'   => $this->domain,
            'regex'    => $regex,
            'path'     => $this->path . $path,
            'key'      => $this->key . '.' . $key,
        ];
    }

}
