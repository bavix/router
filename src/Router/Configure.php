<?php

namespace Bavix\Router;

use Bavix\Exceptions;
use Bavix\Slice\Slice;
use Psr\Cache\CacheItemPoolInterface;

class Configure
{

    use HelperThrows;

    /**
     * @var CacheItemPoolInterface
     */
    protected $pool;

    /**
     * @var Slice
     */
    protected $slice;

    /**
     * @var Slice
     */
    protected $build;

    /**
     * Configure constructor.
     *
     * @param array|\Traversable|Slice $data
     * @param CacheItemPoolInterface|null $pool
     */
    public function __construct($data, CacheItemPoolInterface $pool = null)
    {
        $this->slice = Slice::from($data);
        $this->pool  = $pool;

        $this->types = [
            'http'    => Type\Http::class,
            'prefix'  => Type\Prefix::class,
            'pattern' => Type\Pattern::class,
        ];
    }

    /**
     * @return Slice
     *
     * @return Slice|mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function data()
    {
        if ($this->pool instanceof CacheItemPoolInterface)
        {
            $item = $this->pool->getItem($this->slice);

            if (!$item->isHit())
            {
                $item->set($result = $this->build());
                $this->pool->save($item);

                return $result;
            }

            return $item->get();
        }

        return $this->build();
    }

    /**
     * @return Route[]
     *
     * @throws Exceptions\NotFound\Data
     */
    protected function routes()
    {
        $routes = [];

        foreach ($this->slice->asGenerator() as $key => $slice)
        {
            if ($key{0} === '@')
            {
                // for xml
                continue;
            }

            $type  = $this->getType($slice, $key);
            $class = $this->types[$type];

            /**
             * @var $object Type
             */
            $object = new $class($this, $slice, [
                'key' => $key
            ]);

            $routes += $object->build();
        }

        return $routes;
    }

    /**
     * @return Slice
     */
    public function build()
    {
        if (!$this->build)
        {
            $this->build = $this->slice->make(
                $this->routes()
            );
        }

        return $this->build;
    }

}
