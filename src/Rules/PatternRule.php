<?php

namespace Bavix\Router\Rules;

use Bavix\Exceptions\NotFound\Path;
use Bavix\Router\Rule;

class PatternRule extends Rule
{

    /**
     * PatternRule constructor.
     *
     * @param string $key
     * @param array $storage
     * @param Rule|null $parent
     */
    public function __construct(string $key, array $storage, ?Rule $parent = null)
    {
        parent::__construct($key, $storage, $parent);

        if (!$this->path) {
            throw new Path(
                \sprintf(
                    'No `%s` option found for class `%s` on `%s` route',
                    'path',
                    __CLASS__,
                    $this->getName()
                )
            );
        }
    }

}
