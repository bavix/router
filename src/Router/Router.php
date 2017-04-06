<?php

namespace Deimos\Router;

use Deimos\CacheHelper\SliceHelper;
use Deimos\Slice\Slice;

class Router
{

    protected $classMap = [
        'configure' => Configure::class
    ];

    /**
     * @var Slice
     */
    protected $slice;

    /**
     * @var Configure
     */
    protected $configure;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var SliceHelper
     */
    protected $cache;

    /**
     * Router constructor.
     *
     * @param Slice       $slice
     * @param SliceHelper $cache
     */
    public function __construct(Slice $slice, SliceHelper $cache = null)
    {
        $this->slice  = $slice;
        $this->cache  = $cache;
        $this->method = method();
        $this->scheme = scheme();
        $this->domain = domain();
        $this->path   = path();
    }

    protected function configure()
    {
        if (!$this->configure)
        {
            $class = $this->classMap['configure'];

            $this->configure = new $class($this->slice, $this->cache);
        }

        return $this->configure;
    }

    public function getCurrentRoute()
    {
        return $this->getRoute($this->path);
    }

    public function getRoute($path, $domain = null, $scheme = null)
    {
        return $this->find($this->configure(), [
            'scheme' => $scheme ?? $this->scheme,
            'domain' => $domain ?? $this->domain,
            'path'   => $path,
        ]);
    }

    protected function find(Configure $configure, array $options)
    {
        // domain
        // https://regex101.com/r/0RufFB/1
        // https://regex101.com/r/0RufFB/3

        var_dump($options, $configure->data());

        return null;
    }

}
