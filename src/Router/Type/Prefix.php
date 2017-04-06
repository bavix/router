<?php

namespace Deimos\Router\Type;

use Deimos\Slice\Slice;

class Prefix extends Http
{

    protected function storage($key, Slice $slice)
    {
        list($path, $regex) = $this->path($slice);

        return [
            'defaults' => $this->slice->getData('defaults', []) + $this->defaults,
            'scheme'   => $this->scheme,
            'domain'   => $this->domain,
            'regex'    => $regex,
            'path'     => $this->path . $path,
            'key'      => $this->key . '.' . $key,
        ];
    }

}
