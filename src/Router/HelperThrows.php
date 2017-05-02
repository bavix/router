<?php

namespace Bavix\Router;

use Bavix\Slice\Slice;
use Bavix\Exceptions;

trait HelperThrows
{

    /**
     * @var array
     */
    protected $types = [];

    /**
     * @param Slice  $slice
     * @param string $key
     *
     * @return mixed
     * @throws Exceptions\NotFound\Data
     */
    public function getType(Slice $slice, $key = null)
    {
        $type = $slice->atData('type');

        if ($type === null)
        {
            throw new Exceptions\NotFound\Data('Parameter `type` not found in a route of `' . $key . '`');
        }

        if (!isset($this->types[$type]))
        {
            throw new Exceptions\NotFound\Data('The `' . $type . '` type isn\'t found in a route of `' . $key . '`');
        }

        return $type;
    }

}