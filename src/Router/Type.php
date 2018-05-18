<?php

namespace Bavix\Router;

use Bavix\Exceptions\NotFound;
use Bavix\Slice\Slice;

abstract class Type
{

    use HelperThrows;

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
    protected $protocol;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
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
     */
    abstract public function build(): array;

}
