<?php

namespace Bavix\Router;

abstract class Rule implements \Serializable, \JsonSerializable
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
     * @var array
     */
    protected $middleware;

    /**
     * Rule constructor.
     *
     * @param string $key
     * @param iterable $storage
     * @param null|self $parent
     */
    public function __construct(string $key, $storage, ?self $parent = null)
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
    public function getName(): string
    {
        return $this->_name;
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
    public function getMiddleware(): array
    {
        return (array)$this->middleware;
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
     * @param string $name
     * @param self   $parent
     */
    protected function arrayMerge(string $name, self $parent): void
    {
        $this->$name = \array_merge(
            (array)$parent->$name,
            (array)$this->$name
        );
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
        $this->_name = $parent->_name . '.' . $this->_name;
        $this->methods = $this->methods ?? $parent->methods;
        $this->arrayMerge('defaults', $parent);
        $this->arrayMerge('middleware', $parent);
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

    /**
     * @return array
     * @throws \ReflectionException
     */
    protected function serializeArray(): array
    {
        $properties = [];
        $reflation = new \ReflectionClass($this);
        $protected = $reflation->getProperties(\ReflectionProperty::IS_PROTECTED);
        foreach ($protected as $property) {
            $properties[] = $property->name;
        }
        return $properties;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function __sleep(): array
    {
        return \array_keys($this->serializeArray());
    }

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public function serialize(): string
    {
        $data = [];
        foreach ($this->serializeArray() as $key => $value) {
            $data[$value] = $this->$value;
        }
        return \serialize($data);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized): void
    {
        /**
         * @var string $serialized
         * @var array $data
         */
        $data = \unserialize($serialized, (array)null);
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public function jsonSerialize(): array
    {
        $data = [];
        foreach ($this->serializeArray() as $key => $value) {
            $key = \ltrim($value, '_');
            $data[$key] = $this->$value;
        }
        return $data;
    }

}
