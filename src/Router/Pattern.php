<?php

namespace Bavix\Router;

class Pattern implements PatternResolution
{

    /**
     * if methods === null -> full methods
     *
     * @var null|array
     */
    protected $methods;

    /**
     * @var array
     */
    protected $middleware = [];

    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * Pattern constructor.
     *
     * @param string $path
     * @param string $name
     */
    public function __construct(string $path, ?string $name)
    {
        $this->path = $path;
        $this->name = $name;
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
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * @param array $middleware
     */
    public function setMiddleware(array $middleware): void
    {
        $this->middleware = $middleware;
    }

    /**
     * @return array|null
     */
    public function getMethods(): ?array
    {
        return $this->methods;
    }

    /**
     * @param array|null $methods
     *
     * @return $this
     */
    public function setMethods(?array $methods): self
    {
        $this->methods = $methods;

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
     *
     * @return $this
     */
    public function setDefaults(array $defaults): self
    {
        $this->defaults = $defaults;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            $this->getName() => [
                'type'     => 'pattern',
                'path'     => $this->getPath(),
                'methods'  => $this->getMethods(),
                'defaults' => $this->getDefaults(),
                'middleware' => $this->getMiddleware(),
            ]
        ];
    }

    /**
     * @param array $methods
     *
     * @return Pattern
     */
    public function methods(array $methods): Pattern
    {
        return $this->setMethods($methods);
    }

    /**
     * @return Pattern
     */
    public function any(): self
    {
        return $this->setMethods(null);
    }

    /**
     * @return Pattern
     */
    public function get(): self
    {
        return $this->setMethods(['GET']);
    }

    /**
     * @return Pattern
     */
    public function post(): self
    {
        return $this->setMethods(['POST']);
    }

    /**
     * @return Pattern
     */
    public function put(): self
    {
        return $this->setMethods(['PUT']);
    }

    /**
     * @return Pattern
     */
    public function patch(): self
    {
        return $this->setMethods(['PATCH']);
    }

    /**
     * @return Pattern
     */
    public function delete(): self
    {
        return $this->setMethods(['DELETE']);
    }

}
