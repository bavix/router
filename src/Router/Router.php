<?php

namespace Bavix\Router;

use Bavix\Router\Rules\PatternRule;
use Psr\Cache\CacheItemPoolInterface;
use Bavix\Exceptions;

class Router
{

    /**
     * Router version
     */
    public const VERSION = '2.0.0';

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
    public function __construct($data, CacheItemPoolInterface $pool = null)
    {
        $this->pushConfig($data);
        $this->pool = $pool;
    }

    /**
     * @param array $config
     * @return Router
     */
    protected function pushConfig(array $config): self
    {
        $this->routes = null;
        $this->config = \array_merge(
            (array)$this->config,
            $config
        );

        return $this;
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
        return $this->find(Server::url($path, $host, $protocol));
    }

    /**
     * @return string
     */
    protected function hash(): string
    {
        return self::VERSION . \crc32(\json_encode($this->config));
    }

    /**
     * @return array
     * @throws
     */
    protected function loadingRoutes(): array
    {
        $loader = new Loader($this->config);
        $this->routes = $loader->simplify();

        if ($this->pool) {
            $item = $this->pool->getItem($this->hash());
            $item->set($this->routes);
            $this->pool->save($item);
        }

        return $this->routes;
    }

    /**
     * @return array
     * @throws
     */
    protected function bootRoutes(): array
    {
        if ($this->pool) {
            $item = $this->pool->getItem($this->hash());
            $data = $item->get();
            if ($data) {
                return $data;
            }
        }

        return $this->loadingRoutes();
    }

    /**
     * @return Route[]
     */
    public function routes(): array 
    {
        if (empty($this->routes)) {
            $this->routes = $this->bootRoutes();
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

        if (empty($routes[$path])) {
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
