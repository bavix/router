<?php

namespace Deimos\Router;

use Deimos\Slice\Slice;

class Route
{

    /**
     * @var string
     */
    protected $defaultRegex = '[\w-А-ЯЁа-яё]+';

    /**
     * @var array
     */
    protected $http;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $regex;

    /**
     * @var string
     */
    protected $regexPath;

    /**
     * @var array
     */
    protected $defaults;

    /**
     * @var array
     */
    protected $methods;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var Slice
     */
    protected $slice;

    /**
     * Route constructor.
     *
     * @param Slice  $slice
     * @param string $defaultRegex
     */
    public function __construct(Slice $slice, $defaultRegex = null)
    {
        $this->slice = $slice;

        if ($defaultRegex)
        {
            $this->defaultRegex = $defaultRegex;
        }

        $this->reload();
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getHttp()
    {
        return $this->http;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * @return string
     */
    public function getRegexPath()
    {
        return $this->regexPath;
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    protected function regex()
    {

    }

    protected function reload()
    {
        $this->defaults  = $this->slice->atData('defaults', []);
        $this->regex     = $this->slice->atData('regex', []);
        $this->http      = $this->slice->atData('http', []);
        $this->path      = $this->slice->atData('path');
        $this->regexPath = $this->regex();
    }

}
