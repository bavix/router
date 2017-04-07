<?php

namespace Deimos\Router;

use Deimos\Slice\Slice;

trait HelperThrows
{

    public function getType(Slice $slice, $key = null)
    {
        $type = $slice->atData('type');

        if ($type === null)
        {
            throw new Exceptions\NotFound('Parameter `type` not found in a route of `' . $key . '`');
        }

        if (!isset($this->types[$type]))
        {
            throw new Exceptions\NotFound('The `' . $type . '` type isn\'t found in a route of `' . $key . '`');
        }

        return $type;
    }

}