<?php

namespace Deimos\Router\Type;

use Deimos\Router\Exceptions\NotFound;
use Deimos\Router\Type;
use Deimos\Slice\Slice;

class Http extends Type
{

    /**
     * @var array
     */
    protected $types = [
        'prefix'  => Prefix::class,
        'pattern' => Pattern::class,
    ];

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
            $type = $slice->atData('type');

            if ($type === null)
            {
                throw new NotFound('Parameter `type` not found in a route of `' . $this->key . '.' . $key . '`');
            }

            if (!isset($this->types[$type]))
            {
                throw new NotFound('The `' . $type . '` type isn\'t found in a route of `' . $this->key . '.' . $key . '`');
            }

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
