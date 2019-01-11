<?php

namespace Bavix\Router;

use Bavix\Exceptions\Runtime;

trait Attachable
{

    /**
     * @var string
     */
    protected $_name;

    /**
     * Attachable constructor.
     *
     * @param string $key
     * @param array $storage
     */
    protected function initializer(string $key, array $storage): void
    {
        $this->_name = $key;
        $this->attached($storage);
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
     * @throws
     */
    protected function checkProperty(string $key): void
    {
        if (!\property_exists($this, $key) || \strpos($key, '_') === 0) {
            throw new Runtime(\sprintf('The key `%s` is not registered', $key));
        }
    }

}
