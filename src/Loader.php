<?php

namespace Bavix\Router;

use Bavix\Exceptions;
use Bavix\Exceptions\Runtime;
use Bavix\Router\Rules\PatternRule;
use Bavix\Router\Rules\PrefixRule;

class Loader
{

    /**
     * @var array
     */
    protected $rules = [
        'http' => Rules\HttpRule::class,
        'prefix' => Rules\PrefixRule::class,
        'pattern' => Rules\PatternRule::class,
    ];

    /**
     * @var null|Rule
     */
    protected $parent;

    /**
     * @var array
     */
    protected $config;

    /**
     * Loader constructor.
     *
     * @param iterable $config
     * @param null|Rule $parent
     */
    public function __construct($config, ?Rule $parent = null)
    {
        $this->config = $config;
        $this->parent = $parent;
    }

    /**
     * @return PatternRule[]
     */
    public function simplify(): array
    {
        $routes = [];
        foreach ($this->routes() as $key => $route) {
            $routes[] = $this->_simplify($route, $key);
        }

        return \array_merge(...$routes);
    }

    /**
     * @return \Generator
     */
    public function routes(): \Generator
    {
        foreach ($this->config as $key => $item) {
            if (empty($item['type'])) {
                throw new Exceptions\NotFound\Data(
                    \sprintf('The type parameter for key `%s` is not found', $this->way($key))
                );
            }

            yield $key => $this->rule($item['type'], $key, $item);
        }
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function way(string $key): string
    {
        if ($this->parent) {
            return $this->parent->getName() . '.' . $key;
        }
        return $key;
    }

    /**
     * @param string $type
     * @param string $key
     * @param array $item
     *
     * @return Rule
     */
    public function rule(string $type, string $key, array $item): Rule
    {
        if (!$this->validate($type)) {
            throw new Runtime(\sprintf('Undefined type `%s`', $type));
        }

        $class = $this->rules[$type];
        return new $class($key, $item, $this->parent);
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    protected function validate(string $type): bool
    {
        return \array_key_exists($type, $this->rules);
    }

    /**
     * @param Rule $rule
     * @param string $key
     *
     * @return PatternRule[]
     */
    protected function _simplify(Rule $rule, string $key): array
    {
        if ($rule instanceof PatternRule) {
            return [$key => $rule];
        }

        /**
         * @var PrefixRule $rule
         */
        $rules = [];
        foreach ($rule->resolver() as $index => $item) {
            $location = $key . '.' . $index;
            $rules[] = $this->_simplify($item, $location);
        }

        return \array_merge(...$rules);
    }

}
