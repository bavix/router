<?php

namespace Deimos\Router;

use Deimos\CacheHelper\SliceHelper;
use Deimos\Slice\Slice;

class Router
{

    protected $classMap = [
        'configure' => Configure::class
    ];

    /**
     * @var Slice
     */
    protected $slice;

    /**
     * @var Configure
     */
    protected $configure;

    /**
     * @var string
     */
    protected $method;

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
     * @var SliceHelper
     */
    protected $cache;

    /**
     * Router constructor.
     *
     * @param Slice       $slice
     * @param SliceHelper $cache
     */
    public function __construct(Slice $slice, SliceHelper $cache = null)
    {
        $this->slice  = $slice;
        $this->cache  = $cache;
        $this->method = method();
        $this->scheme = scheme();
        $this->domain = domain();
        $this->path   = path();
    }

    protected function configure()
    {
        if (!$this->configure)
        {
            $class = $this->classMap['configure'];

            $this->configure = new $class($this->slice, $this->cache);
        }

        return $this->configure;
    }

    /**
     * @return Route
     * @throws Exceptions\NotFound
     * @throws \Deimos\CacheHelper\Exceptions\PermissionDenied
     * @throws \Deimos\Helper\Exceptions\ExceptionEmpty
     */
    public function getCurrentRoute()
    {
        return $this->getRoute($this->path);
    }

    /**
     * @param string $path
     * @param string $domain
     * @param string $scheme
     *
     * @return Route
     * @throws Exceptions\NotFound
     * @throws \Deimos\CacheHelper\Exceptions\PermissionDenied
     * @throws \Deimos\Helper\Exceptions\ExceptionEmpty
     */
    public function getRoute($path, $domain = null, $scheme = null)
    {
        $uri = ($scheme ?? $this->scheme) . '://' . ($domain ?? $this->domain) . $path;

        return $this->find($this->configure(), $uri);
    }

    /**
     * @param Configure $configure
     * @param string    $uri
     *
     * @return Route
     * @throws Exceptions\NotFound
     * @throws \Deimos\CacheHelper\Exceptions\PermissionDenied
     * @throws \Deimos\Helper\Exceptions\ExceptionEmpty
     */
    protected function find(Configure $configure, $uri)
    {
        $slice = $configure->data();

        /**
         * @var Route $route
         */
        foreach ($slice->asArray() as $key => $route)
        {
            if ($route->test($uri, $this->method))
            {
                return $route;
            }
        }

        throw new Exceptions\NotFound('Page `' . $uri . '` not found', 404);
    }

}
