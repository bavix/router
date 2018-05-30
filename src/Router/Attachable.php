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
     */
    protected function attached(array $storage): void
    {
        foreach ($storage as $key => $value) {
            if ($key{0} === '_' || !\property_exists($this, $key)) {
                throw new Runtime(\sprintf('The key `%s` is not registered', $key));
            }

            if (null === $value || $key{0} === '@') {
                // skip
                continue;
            }

            $this->$key = $value;
        }
    }

}
