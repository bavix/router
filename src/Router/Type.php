<?php

namespace Bavix\Router;

use Bavix\Exceptions\NotFound;
use Bavix\Slice\Slice;

/**
 * Class Type
 * @package Bavix\Router
 *
 * @deprecated
 */
abstract class Type
{

    use HelperThrows;

    /**
     * @var bool
     *
     * @deprecated
     */
    protected $pathRequired;

    /**
     * @var Configure
     *
     * @deprecated
     */
    protected $configure;

    /**
     * @var Slice
     *
     * @deprecated
     */
    protected $slice;

    /**
     * @var array
     *
     * @deprecated
     */
    protected $regex;

    /**
     * @var array
     *
     * @deprecated
     */
    protected $defaults;

    /**
     * @var array
     *
     * @deprecated
     */
    protected $methods;

    /**
     * @var string
     *
     * @deprecated
     */
    protected $protocol;

    /**
     * @var string
     *
     * @deprecated
     */
    protected $host;

    /**
     * @var string
     *
     * @deprecated
     */
    protected $path;

    /**
     * @var string
     *
     * @deprecated
     */
    protected $key;

    /**
     * Type constructor.
     *
     * @param Configure $configure
     * @param Slice     $slice
     * @param array     $options
     *
     * @throws NotFound\Data
     *
     * @deprecated
     */
    public function __construct(Configure $configure, Slice $slice, array $options)
    {
        $this->configure = $configure;
        $this->slice     = $slice;

        $this->protocol = $options['protocol'] ?? null;
        $this->host     = $options['host'] ?? null;
        $this->key      = $options['key'] ?? null;

        $data = $this->path($slice);

        $this->path     = $options['path'] ?? $data[0];
        $this->regex    = $options['regex'] ?? $data[1];
        $this->defaults = (array)($options['defaults'] ?? $slice->getData('defaults'));
        $this->methods  = (array)(empty($options['methods']) ? $slice->getData('methods') : $options['methods']);
    }

    /**
     * @param string|array $storage
     * 
     * @return string
     *
     * @deprecated
     */
    protected function regexData(&$storage): array
    {
        $regex = (array)$this->regex;

        if (\is_array($storage) && isset($storage[1]))
        {
            $regex   = $storage[1] + $regex;
            $storage = $storage[0];
        }

        return $regex;
    }

    /**
     * @param Slice $slice
     *
     * @return array
     * @throws NotFound\Path
     *
     * @deprecated
     */
    protected function path(Slice $slice): array 
    {
        $path = $slice->getData('path', $this->path);

        if ($this->pathRequired && !$path)
        {
            throw new NotFound\Path('Parameter `path` not found in a route of `' . $this->key . '`');
        }

        $regex = $this->regexData($path);

        return [$path, $regex];
    }

    /**
     * @return array
     *
     * @deprecated
     */
    abstract public function build(): array;

}
