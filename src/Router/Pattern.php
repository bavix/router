<?php

namespace Bavix\Router;

class Pattern
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
        return $this->name ?: $this->generateName();
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
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
                'type' => 'pattern',
                'path' => $this->getPath(),
                'methods' => $this->getMethods(),
                'defaults' => $this->getDefaults(),
            ]
        ];
    }

    /**
     * @return string
     */
    protected function generateName(): string
    {
        return \preg_replace('~[^\w-]~', '_', $this->path);
    }

}
