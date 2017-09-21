<?php

namespace Bavix\Router\Type;

use Bavix\Exceptions\NotFound;
use Bavix\Router\Configure;
use Bavix\Router\Type;
use Bavix\Slice\Slice;

class Http extends Type
{

    /**
     * Http constructor.
     *
     * @param Configure $configure
     * @param Slice     $slice
     * @param array     $options
     *
     * @throws NotFound\Data
     */
    public function __construct(Configure $configure, Slice $slice, array $options)
    {
        $this->types = [
            'prefix'  => Prefix::class,
            'pattern' => Pattern::class,
        ];

        parent::__construct($configure, $slice, $options);
    }

    /**
     * @param string $key
     * @param Slice  $slice
     *
     * @return array
     * @throws NotFound\Data
     */
    protected function storage($key, Slice $slice)
    {
        list($path, $regex) = $this->path($slice);

        return [
            'protocol' => $this->slice->getData('protocol', $this->protocol),
            'host'     => $this->slice->getData('host', $this->host),
            'regex'    => $regex,
            'path'     => $this->path . $path, // http add path
            'key'      => $this->key . '.' . $key,
            'defaults' => $this->slice->getData('defaults', []),
            'methods'  => $this->slice->getData('methods', []),
        ];
    }

    /**
     * @return array
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
