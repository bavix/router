<?php

namespace Deimos\Router;

use Deimos\Router\Exceptions\NotFound;
use Deimos\Slice\Slice;

abstract class Type
{

    /**
     * @var bool
     */
    protected $pathRequired;

    /**
     * @var Configure
     */
    protected $configure;

    /**
     * @var Slice
     */
    protected $slice;

    /**
     * @var array
     */
    protected $regex;

    /**
     * @var array
     */
    protected $defaults;

    /**
     * @var array
     */
    protected $methods;

    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var array
     */
    protected $types;

    /**
     * Type constructor.
     *
     * @param Configure $configure
     * @param Slice     $slice
     * @param array     $options
     *
     * @throws NotFound
     */
    public function __construct(Configure $configure, Slice $slice, array $options)
    {
        $this->configure = $configure;
        $this->slice     = $slice;

        $this->scheme = $options['scheme'] ?? null;
        $this->domain = $options['domain'] ?? null;
        $this->key    = $options['key'] ?? null;

        $data = $this->path($slice);

        $this->path     = $options['path'] ?? $data[0];
        $this->regex    = $options['regex'] ?? $data[1];
        $this->defaults = (array)($options['defaults'] ?? $slice->getData('defaults'));
        $this->methods  = (array)($options['methods'] ?? $slice->getData('methods'));
    }

    /**
     * @param Slice $slice
     *
     * @return array
     * @throws NotFound
     */
    protected function path(Slice $slice)
    {
        $path = $slice->getData('path', $this->path);

        if ($this->pathRequired && !$path)
        {
            throw new NotFound('Parameter `path` not found in a route of `' . $this->key . '`');
        }

        $regex = $this->regex ?? [];

        if (is_array($path) && isset($path[1]))
        {
            $regex = $path[1] + $regex;
            $path  = $path[0];
        }

        return [$path, $regex];
    }

    /**
     * @return array
     */
    abstract public function build();

}