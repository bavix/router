<?php

namespace Bavix\Router;

class Group
{

    /**
     * @var ResourceCollection[]
     */
    protected $collections = [];

    /**
     * @var Pattern[]
     */
    protected $resolver = [];

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $protocol;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var array
     */
    protected $middleware = [];

    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * @var array
     */
    protected $methods;

    /**
     * Group constructor.
     *
     * @param null|string $prefix
     * @param callable $callback
     */
    public function __construct(string $prefix, callable $callback)
    {
        $resolver = new Resolver(function (Pattern $pattern) {
            return $this->pushPattern($pattern);
        }, function (ResourceCollection $collection) {
            return $this->pushCollection($collection);
        });

        $this->path = $prefix;
        $closure = \Closure::fromCallable($callback);
        $closure->call($resolver, $resolver);
    }

    /**
     * @param Pattern $pattern
     *
     * @return Pattern
     */
    protected function pushPattern($pattern): Pattern
    {
        $this->resolver[$pattern->getName()] = $pattern;
        return $pattern;
    }

    /**
     * @param ResourceCollection $collection
     *
     * @return ResourceCollection
     */
    protected function pushCollection(ResourceCollection $collection): ResourceCollection
    {
        $this->collections[] = $collection;
        return $collection;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name ?: Helper::generateName($this->path);
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    /**
     * @param string $protocol
     * @return $this
     */
    public function setProtocol(string $protocol): self
    {
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return $this
     */
    public function setHost(string $host): self
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * @param array $middleware
     * @return $this
     */
    public function setMiddleware(array $middleware): self
    {
        $this->middleware = $middleware;
        return $this;
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * @param array $defaults
     * @return $this
     */
    public function setDefaults(array $defaults): self
    {
        $this->defaults = $defaults;
        return $this;
    }

    /**
     * @return null|array
     */
    public function getMethods(): ?array
    {
        return $this->methods;
    }

    /**
     * @param null|array $methods
     * @return $this
     */
    public function setMethods(?array $methods): self
    {
        $this->methods = $methods;
        return $this;
    }

    /**
     * @return \Generator
     */
    protected function patterns(): \Generator
    {
        foreach ($this->collections as $collection) {
            foreach ($collection as $pattern) {
                yield $pattern;
            }
        }
    }

    /**
     * @return array
     */
    protected function getResolver(): array
    {
        $patterns = [];

        /**
         * @var Pattern[] $resolver
         */
        $resolver = \array_merge(
            $this->resolver,
            \iterator_to_array($this->patterns())
        );

        foreach ($resolver as $pattern) {
            $patterns[] = $pattern->toArray();
        }

        return \array_merge(...$patterns);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            $this->getName() => [
                'type' => 'prefix',
                'protocol' => $this->getProtocol(),
                'host' => $this->getHost(),
                'path' => $this->path,
                'methods' => $this->getMethods(),
                'resolver' => $this->getResolver(),
                'defaults' => $this->getDefaults(),
                'middleware' => $this->getMiddleware(),
            ]
        ];
    }

}
