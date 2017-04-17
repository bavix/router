<?php

namespace Deimos\Router\Type;

use Deimos\Slice\Slice;

class Prefix extends Http
{

    /**
     * @var bool
     */
    protected $pathRequired = true;

    /**
     * @param string $key
     * @param Slice  $slice
     *
     * @return array
     * @throws \Deimos\Router\Exceptions\NotFound
     */
    protected function storage($key, Slice $slice)
    {
        list($path, $regex) = $this->path($slice);

        $methods = $this->slice->getData('methods');

        return [
            'defaults' => (array)$this->slice->getData('defaults') + $this->defaults,
            'methods'  => empty($methods) ? $this->methods : $methods,
            'scheme'   => $this->scheme,
            'domain'   => $this->domain,
            'regex'    => $regex,
            'path'     => $this->path . $path,
            'key'      => $this->key . '.' . $key,
        ];
    }

}
