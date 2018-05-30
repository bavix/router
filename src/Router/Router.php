<?php

namespace Bavix\Router;

use Bavix\Router\Rules\PatternRule;
use Psr\Cache\CacheItemPoolInterface;
use Bavix\Exceptions;

class Router
{

    /**
     * @var PatternRule[]
     */
    protected $routes;

    /**
     * @var iterable
     */
    protected $config;

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
     * @return Route
     *
     * @throws Exceptions\NotFound\Data
     * @throws Exceptions\NotFound\Path
     */
    public function getCurrentRoute(): Route
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
     * @throws Exceptions\NotFound\Path
     */
    public function getRoute(string $path, string $host = null, string $protocol = null): Route
    {
        return $this->find(Build::url($path, $host, $protocol));
    }

    /**
     * @return Route[]
     */
    public function routes(): array 
    {
        if (empty($this->routes))
        {
            $loader = new Loader($this->config);
            $this->routes = $loader->simplify();
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
        $routes = $this->routes();

        if (empty($routes[$path]))
        {
            throw new Exceptions\NotFound\Path('Route `' . $path . '` not found');
        }

        return $routes[$path];
    }

    /**
     * @param string $subject
     * @return Route
     */
    protected function find(string $subject): Route
    {
        foreach ($this->routes() as $name => $patternRule) {
            $match = new Match($patternRule, $subject, Server::sharedInstance()->method());
            if ($match->isTest()) {
                return new Route($match);
            }
        }

        throw new Exceptions\NotFound\Page('Page `' . $subject . '` not found', 404);
    }

}
