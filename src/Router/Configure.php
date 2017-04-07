<?php

namespace Deimos\Router;

use Deimos\CacheHelper\SliceHelper;
use Deimos\Router\Exceptions\NotFound;
use Deimos\Slice\Slice;

class Configure
{

    use HelperThrows;

    /**
     * @var array
     */
    protected $types = [
        'http'    => Type\Http::class,
        'prefix'  => Type\Prefix::class,
        'pattern' => Type\Pattern::class,
    ];

    /**
     * @var SliceHelper
     */
    protected $cache;

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
     * @param Slice            $slice
     * @param SliceHelper|null $cache
     */
    public function __construct(Slice $slice, SliceHelper $cache = null)
    {
        $this->slice  = $slice;
        $this->cache  = $cache;
    }

    /**
     * @return Slice
     *
     * @throws NotFound
     * @throws \Deimos\CacheHelper\Exceptions\PermissionDenied
     * @throws \Deimos\Helper\Exceptions\ExceptionEmpty
     */
    public function data()
    {
        if ($this->cache)
        {
            if (!$this->cache->valid($this->slice))
            {
                $data = $this->build();
                $this->cache->save($this->slice, $data);
            }

            return require $this->cache->getCachePath($this->slice);
        }

        return $this->build();
    }

    /**
     * @return Slice
     *
     * @throws \Deimos\Helper\Exceptions\ExceptionEmpty
     * @throws NotFound
     */
    public function build()
    {
        if ($this->build)
        {
            return $this->build;
        }

        $routes = [];

        foreach ($this->slice->asGenerator() as $key => $slice)
        {
            $type  = $this->getType($slice, $key);
            $class = $this->types[$type];

            /**
             * @var $object Type
             */
            $object = new $class($this, $slice, [
                'key'    => $key
            ]);

            $routes += $object->build();
        }

        $this->build = $this->slice->make($routes);

        return $this->build;
    }

}
