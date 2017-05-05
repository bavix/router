<?php

namespace Bavix\Router\Type;

use Bavix\Slice\Slice;

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
     */
    protected function storage($key, Slice $slice)
    {
        list($path, $regex) = $this->path($slice);

        $methods = $this->slice->getData('methods');

        return [
            'defaults' => (array)$this->slice->getData('defaults') + $this->defaults,
            'methods'  => empty($methods) ? $this->methods : $methods,
            'protocol' => $this->protocol,
            'host'     => $this->host,
            'regex'    => $regex,
            'path'     => $this->path . $path,
            'key'      => $this->key . '.' . $key,
        ];
    }

}
