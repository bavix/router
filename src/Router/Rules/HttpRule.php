<?php

namespace Bavix\Router\Rules;

/**
 * Class HttpRule
 *
 * @package Bavix\Router\Rules
 *
 * @deprecated use PrefixRule
 * @since 2.0.0
 */
final class HttpRule extends PrefixRule
{

    /**
     * @return string
     */
    protected function deprecated(): string
    {
        return \sprintf(
            '%s: Type `%s` %s, use `%s`',
            __CLASS__,
            'http',
            __FUNCTION__,
            'prefix'
        );
    }

    /**
     * HttpRule constructor.
     *
     * @param string $key
     * @param array  $storage
     * @param null   $parent
     */
    public function __construct(string $key, array $storage, $parent = null)
    {
        \trigger_error(
            $this->deprecated(),
            \E_USER_DEPRECATED
        );

        parent::__construct($key, $storage, $parent);
    }

}
