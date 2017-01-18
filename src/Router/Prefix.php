<?php

namespace Deimos\Router;

use Deimos\Route\Route;
use Deimos\Router\Exceptions\PathNotFound;
use Deimos\Router\Exceptions\TypeNotFound;

class Prefix
{

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $resolver;

    /**
     * @var array
     */
    protected $defaults;

    /**
     * Prefix constructor.
     *
     * @param array $resolver
     * @param array $defaults
     */
    public function __construct(array $resolver, array $defaults = [])
    {
        $this->resolver = $resolver;
        $this->defaults = $defaults;
    }

    /**
     * @param $value
     */
    public function setPath($value)
    {
        $this->path = $value;
    }

    /**
     * @param array  $array
     * @param string $key
     *
     * @return array
     */
    protected function getArray(array $array, $key)
    {
        return isset($array[$key]) ? $array[$key] : [];
    }

    /**
     * @return array
     * @throws PathNotFound
     * @throws TypeNotFound
     */
    public function getResolver()
    {
        return $this->resolver($this->resolver);
    }

    /**
     * @return string
     */
    protected function path()
    {
        return ($this->path ?: '');
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    public function defaults(array $rows)
    {
        return array_merge($this->defaults, $rows);
    }

    /**
     * @param array $resolver
     *
     * @return array
     * @throws PathNotFound
     * @throws TypeNotFound
     */
    protected function resolver(array $resolver)
    {
        $rows = [];

        foreach ($resolver as $key => $item)
        {
            if (!isset($item['type']))
            {
                throw new TypeNotFound($key);
            }

            if (!isset($item['path']))
            {
                throw new PathNotFound($key);
            }

            if ($item['type'] === 'prefix')
            {
                $prefix = new Prefix(
                    $this->getArray($item, 'resolver'),
                    $this->defaults(
                        $this->getArray($item, 'defaults')
                    )
                );

                $prefix->setPath($this->path() . $item['path']);

                foreach ($prefix->getResolver() as $pattern)
                {
                    $rows[] = $pattern;
                }

                continue;
            }

            $path    = (array)$item['path'];
            $path[0] = $this->path() . $path[0];

            $rows[] = new Route(
                $path,
                $this->defaults(
                    $this->getArray($item, 'defaults')
                ),
                $this->getArray($item, 'methods')
            );
        }

        return $rows;
    }

}