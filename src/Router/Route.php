<?php

namespace Deimos\Router;

use Deimos\Route\Route as ClassRoute;

class Route
{

    /**
     * @var \Deimos\Route\Route
     */
    protected $route;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * Route constructor.
     *
     * @param ClassRoute $route
     * @param array      $attributes
     */
    public function __construct(ClassRoute $route, array $attributes)
    {
        $this->route = $route;

        foreach ($attributes as $key => $match)
        {
            if (is_int($key) || empty($match))
            {
                unset($attributes[$key]);
            }
        }

        $this->attributes = array_merge($this->route->attributes(), $attributes);
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return $this->attributes;
    }

}
