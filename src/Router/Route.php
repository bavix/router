<?php

namespace Bavix\Router;

use Bavix\Router\Rules\PatternRule;

class Route implements \Serializable
{

    /**
     * @var PatternRule
     */
    protected $rule;

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        return $this->rule->getDefaults();
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes ?? $this->getDefaults();
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    protected function methodAllowed(string $method): bool
    {
        $methods = $this->rule->getMethods();
        return $methods === null || \in_array($method, $methods, true);
    }

    /**
     * @param string $uri
     *
     * @return Match
     */
    protected function match(string $uri): Match
    {
        return new Match($this->rule, $uri);
    }

    /**
     * @param string $uri
     * @param string $method
     *
     * @return bool
     */
    public function test(string $uri, string $method): bool
    {
        return $this->methodAllowed($method) && $this->match($uri)->isTest();
    }

    /**
     * @inheritdoc
     */
    public function serialize(): string
    {

    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized): void
    {

    }

}
