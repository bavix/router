<?php

namespace Bavix\Router;

class Route implements Routable
{

    /**
     * @var Match
     */
    protected $match;

    /**
     * @param Match $match
     */
    public function __construct(Match $match)
    {
        $this->match = $match;
    }

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->match->getProtocol();
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->match->getHost();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->match->getRule()->getName();
    }

    /**
     * @return string
     */
    public function getPathValue(): string
    {
        return $this->match->getRule()->getPath()->getValue();
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->match->getPath();
    }

    /**
     * @return string
     */
    public function getPathPattern(): string
    {
        return $this->match->getRule()->getPath()->getPattern();
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->match->getPattern();
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->match->getAttributes();
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        return $this->match->getRule()->getDefaults();
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->match->getGroups();
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->match->getRule()->getMethods();
    }

    /**
     * @return array
     */
    public function __sleep(): array
    {
        return ['match'];
    }

    /**
     * @inheritdoc
     */
    public function serialize(): string
    {
        return \serialize($this->match);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized): void
    {
        $this->match = \unserialize($serialized, (array)null);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return [
            'protocol' => $this->getProtocol(),
            'host' => $this->getHost(),
            'name' => $this->getName(),
            'path' => $this->getPath(),
            'pathValue' => $this->getPathValue(),
            'pathPattern' => $this->getPathPattern(),
            'pattern' => $this->getPattern(),
            'attributes' => $this->getAttributes(),
            'defaults' => $this->getDefaults(),
            'methods' => $this->getMethods(),
        ];
    }

}
