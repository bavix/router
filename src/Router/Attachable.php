<?php

namespace Bavix\Router;

use Bavix\Exceptions\Runtime;

trait Attachable
{

    /**
     * @var string
     */
    protected $_key;

    /**
     * Attachable constructor.
     *
     * @param string $key
     * @param array $storage
     */
    protected function initializer(string $key, array $storage): void
    {
        $this->_key = $key;
        $this->attached($storage);
    }

    /**
     * @param array $storage
     * @return array
     */
    protected function filter(array $storage): array
    {
        return \array_filter($storage, function (string $key) {
            $this->checkProperty($key);
            return $key{0} !== '@'; // for xml
        }, \ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param string $key
     */
    protected function checkProperty(string $key): void
    {
        if ($key{0} === '_' || !\property_exists($this, $key)) {
            throw new Runtime(\sprintf('The key `%s` is not registered', $key));
        }
    }

    /**
     * @param array $storage
     */
    protected function attached(array $storage): void
    {
        foreach ($this->filter($storage) as $key => $value) {
            $this->$key = $value;
        }
    }

}
