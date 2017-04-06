<?php

namespace Deimos\Router;

use Deimos\Slice\Slice;

class Route
{

    /**
     * @var Slice
     */
    protected $slice;

    /**
     * Route constructor.
     *
     * @param Slice $slice
     */
    public function __construct(Slice $slice)
    {
        $this->slice = $slice;
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return $this->slice->getData('attributes', []);
    }

}
