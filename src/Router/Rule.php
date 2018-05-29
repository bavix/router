<?php

namespace Bavix\Router;

abstract class Rule
{

    use Attachable;

    /**
     * @var null|string
     */
    protected $type;

    /**
     * @var null|string
     */
    protected $path;

    /**
     * @var array
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
        if ($parent) {
            $this->afterPrepare($parent);
        }
    }

    /**
     * @return void
     */
    protected function prepare(): void
    {
        // todo: a lot of logic
    }

    /**
     * @param self $parent
     * @return void
     */
    protected function afterPrepare(self $parent): void
    {
//        if ($parent->path) {
//            $this->path = new RegExp(
//                $parent->path->regExp() . $this->path()->regExp(),
//                $parent->path->value() . $this->path()->value()
//            );
//        }

        $this->path = $parent->path . $this->path;
        $this->_key = $parent->_key . '.' . $this->_key;
        $this->methods = $this->methods ?? $parent->methods;
        $this->defaults = \array_merge(
            (array)$parent->defaults,
            (array)$this->defaults
        );
    }

}
