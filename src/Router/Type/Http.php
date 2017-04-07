<?php

namespace Deimos\Router\Type;

use Deimos\Router\Exceptions\NotFound;
use Deimos\Router\HelperThrows;
use Deimos\Router\Type;
use Deimos\Slice\Slice;

class Http extends Type
{

    use HelperThrows;

    /**
     * @var array
     */
    protected $types = [
        'prefix'  => Prefix::class,
        'pattern' => Pattern::class,
    ];

    /**
     * @param string $key
     * @param Slice  $slice
     *
     * @return array
     * @throws NotFound
     */
    protected function storage($key, Slice $slice)
    {
        list($path, $regex) = $this->path($slice);

        return [
            'scheme'  => $this->slice->getData('scheme', $this->scheme),
            'domain'  => $this->slice->getData('domain', $this->domain),
            'regex'   => $regex,
            'path'    => $path,
            'key'     => $this->key . '.' . $key,
            'methods' => $this->slice->getData('methods', []),
        ];
    }

    /**
     * @return array
     * @throws \Deimos\Helper\Exceptions\ExceptionEmpty
     * @throws NotFound
     */
    public function build()
    {
        $routes   = [];
        $resolver = $this->slice->getSlice('resolver');

        foreach ($resolver->asGenerator() as $key => $slice)
        {
            $type  = $this->getType($slice, $this->key . '.' . $key);
            $class = $this->types[$type];

            /**
             * @var $object Type
             */
            $object = new $class(
                $this->configure,
                $slice,
                $this->storage($key, $slice)
            );

            $routes += $object->build();
        }

        return $routes;
    }

}
