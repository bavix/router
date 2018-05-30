<?php

namespace Bavix\Router;

abstract class Rule
{

    use Attachable;

    /**
     * @var string
     */
    protected $_protocol;

    /**
     * @var string
     */
    protected $_host;

    /**
     * @var null|string
     */
    protected $type;

    /**
     * @var null|Path
     */
    protected $path;

    /**
     * @var null|array
     */
    protected $methods;

    /**
     * @var array
     */
    protected $defaults;

    /**
     * Rule constructor.
     *
     * @param string $key
     * @param array $storage
     * @param null|self $parent
     */
    public function __construct(string $key, array $storage, ?self $parent = null)
    {
        $this->prepare();
        $this->initializer($key, $storage);
        $this->pathInit();
        if ($parent) {
            $this->afterPrepare($parent);
        }
    }

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->_protocol;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->_host;
    }

    /**
     * @return Path
     */
    public function getPath(): Path
    {
        return $this->path;
    }

    /**
     * @return null|array
     */
    public function getMethods(): ?array
    {
        return $this->methods;
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        return (array)$this->defaults;
    }

    /**
     * @param Path|null $path
     */
    protected function hinge(?Path $path): void
    {
        if ($this->path && $path) {
            $this->path->hinge($path);
            return;
        }

        $this->path = $path ?? $this->path;
    }

    /**
     * @param self $parent
     * @return void
     */
    protected function afterPrepare(self $parent): void
    {
        $this->hinge($parent->path);
        $this->_protocol = $parent->protocol ?? $parent->_protocol ?? $this->_protocol;
        $this->_host = $parent->host ?? $parent->_host ?? $this->_host;
        $this->_key = $parent->_key . '.' . $this->_key;
        $this->methods = $this->methods ?? $parent->methods;
        $this->defaults = \array_merge(
            (array)$parent->defaults,
            (array)$this->defaults
        );
    }

    /**
     * if this.path === string then new Path(string, [])
     * else this.path === array then new Path(...this.path)
     */
    protected function pathInit(): void
    {
        if (\is_string($this->path)) {
            $this->path = new Path($this->path);
        } elseif (\is_array($this->path)) {
            $this->path = new Path(...$this->path);
        }
    }

    /**
     * @return void
     */
    protected function prepare(): void
    {
        $this->_protocol = '\w+';
        $this->_host = '[^\/]+';
    }

}
