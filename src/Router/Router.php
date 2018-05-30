<?php

namespace Bavix\Router;

use Bavix\Exceptions;
use Psr\Cache\CacheItemPoolInterface;

class Router
{

    /**
     * @var CacheItemPoolInterface
     */
    protected $pool;

    /**
     * Router constructor.
     *
     * @param iterable $data
     * @param CacheItemPoolInterface   $pool
     */
    public function __construct(iterable $data, CacheItemPoolInterface $pool = null)
    {
        $this->config = $data;
        $this->pool = $pool;
    }

    /**
     * @return Match
     *
     * @throws Exceptions\NotFound\Data
     */
    public function getCurrentRoute(): Match
    {
        return $this->find(Server::sharedInstance()->path());
    }

    /**
     * @param string $path
     * @param string $host
     * @param string $protocol
     *
     * @return Route
     * @throws Exceptions\NotFound\Data
     */
    public function getRoute(string $path, string $host = null, string $protocol = null): Route
    {
        $uri = ($protocol ?? $this->protocol) . '://' . ($host ?? $this->host) . $path;

        return $this->find($uri);
    }

    /**
     * @return Route[]
     */
    public function routes(): array 
    {
        if (empty($this->routes))
        {
            $this->routes = (new Loader($this->config));
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
    public function route(string $path): Route
    {
        $route = $this->configureSlice()->atData($path);

        if (empty($route))
        {
            throw new Exceptions\NotFound\Path('Route `' . $path . '` not found');
        }

        return $route;
    }

}
