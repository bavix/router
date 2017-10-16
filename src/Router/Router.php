<?php

namespace Bavix\Router;

use Bavix\Exceptions;
use Bavix\Slice\Slice;
use Psr\Cache\CacheItemPoolInterface;

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
     * @var CacheItemPoolInterface
     */
    protected $pool;

    /**
     * Router constructor.
     *
     * @param array|\Traversable|Slice $data
     * @param CacheItemPoolInterface   $pool
     */
    public function __construct($data, CacheItemPoolInterface $pool = null)
    {
        $this->slice    = Slice::from($data);
        $this->pool     = $pool;
        $this->method   = method();
        $this->protocol = protocol();
        $this->host     = host();
        $this->path     = path();
    }

    /**
     * @return Configure
     */
    protected function configure()
    {
        if (!$this->configure)
        {
            $class = $this->classMap['configure'];

            $this->configure = new $class($this->slice, $this->pool);
        }

        return $this->configure;
    }

    /**
     * @return Route
     *
     * @throws Exceptions\NotFound\Data
     */
    public function getCurrentRoute()
    {
        return $this->getRoute($this->path);
    }

    /**
     * @param string $path
     * @param string $host
     * @param string $protocol
     *
     * @return Route
     * @throws Exceptions\NotFound\Data
     */
    public function getRoute($path, $host = null, $protocol = null)
    {
        $uri = ($protocol ?? $this->protocol) . '://' . ($host ?? $this->host) . $path;

        return $this->find($uri);
    }

    /**
     * @return Slice
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
     */
    public function routes()
    {
        if (empty($this->routes))
        {
            $this->routes = $this->configureSlice()->asArray();
        }

        return $this->routes;
    }

    /**
     * @param string $path
     *
     * @return Route
     *
     * @throws Exceptions\NotFound\Path
     */
    public function route($path)
    {
        $route = $this->configureSlice()->atData($path);

        if (empty($route))
        {
            throw new Exceptions\NotFound\Path('Route `' . $path . '` not found');
        }

        return $route;
    }

    /**
     * @param $uri
     *
     * @return Route
     *
     * @throws Exceptions\NotFound\Page
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

        throw new Exceptions\NotFound\Page('Page `' . $uri . '` not found', 404);
    }

}
