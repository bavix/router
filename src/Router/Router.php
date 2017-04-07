<?php

namespace Deimos\Router;

use Deimos\CacheHelper\SliceHelper;
use Deimos\Slice\Slice;

class Router
{

    /**
     * @var array
     */
    protected $classMap = [
        'configure' => Configure::class
    ];

    /**
     * @var Slice
     */
    protected $slice;

    /**
     * @var Slice
     */
    protected $configureSlice;

    /**
     * @var Route[]
     */
    protected $routes;

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

    /**
     * @return Configure
     */
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
     *
     * @throws Exceptions\NotFound
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
     */
    public function getRoute($path, $domain = null, $scheme = null)
    {
        $uri = ($scheme ?? $this->scheme) . '://' . ($domain ?? $this->domain) . $path;

        return $this->find($uri);
    }

    /**
     * @return Slice
     * @throws \Deimos\CacheHelper\Exceptions\PermissionDenied
     * @throws \Deimos\Helper\Exceptions\ExceptionEmpty
     */
    protected function configureSlice()
    {
        if (!$this->configureSlice)
        {
            $this->configureSlice = $this->configure()->data();
        }

        return $this->configureSlice;
    }

    /**
     * @return Route[]
     *
     * @throws \Deimos\CacheHelper\Exceptions\PermissionDenied
     * @throws \Deimos\Helper\Exceptions\ExceptionEmpty
     */
    public function routes()
    {
        if (!$this->routes)
        {
            $this->routes = $this->configureSlice()->asArray();
        }

        return $this->routes;
    }

    /**
     * @param string $path
     *
     * @return Route
     * @throws Exceptions\NotFound
     * @throws \Deimos\CacheHelper\Exceptions\PermissionDenied
     * @throws \Deimos\Helper\Exceptions\ExceptionEmpty
     */
    public function route($path)
    {
        $route = $this->configureSlice()->atData($path);

        if (!$route)
        {
            throw new Exceptions\NotFound('Route `' . $path . '` not found');
        }

        return $route;
    }

    /**
     * @param $uri
     *
     * @return Route
     * @throws Exceptions\NotFound
     * @throws \Deimos\CacheHelper\Exceptions\PermissionDenied
     * @throws \Deimos\Helper\Exceptions\ExceptionEmpty
     */
    protected function find($uri)
    {
        /**
         * @var Route $route
         */
        foreach ($this->routes() as $key => $route)
        {
            if ($route->test($uri, $this->method))
            {
                return $route;
            }
        }

        throw new Exceptions\NotFound('Page `' . $uri . '` not found', 404);
    }

}
