<?php

namespace Deimos\Router;

use Deimos\Slice\Slice;

class Route implements \Serializable
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
     * @var string
     */
    protected $filterPath;

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
     * @return string
     */
    public function getFilterPath()
    {
        return $this->filterPath;
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
    protected function pathFilter()
    {
        return preg_replace_callback(
            '~\<(?<key>\w+)(\:(?<value>.+?))?\>~',
            function ($matches)
            {
                if (!empty($matches['value']) && empty($this->regex[$matches['key']]))
                {
                    $this->regex[$matches['key']] = $matches['value'];
                }

                return '<' . $matches['key'] . '>';
            },
            $this->path
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function quote($path)
    {
        $path = preg_quote($path, '()');
        $path = strtr($path, [
            '\\(' => '(',
            '\\)' => ')',
            '\\<' => '<',
            '\\>' => '>',
        ]);

        return $this->optional($path);
    }

    /**
     * @param string $rulePath
     *
     * @return string
     */
    protected function optional($rulePath)
    {
        return str_replace(')', ')?', $rulePath);
    }

    /**
     * @param string $route
     *
     * @return string
     */
    protected function toRegex($route)
    {
        $path = $this->quote($route);

        return preg_replace_callback(
            '~\<(?<key>[\w-]+)\>~',
            function ($matches)
            {
                return '(?<' . $matches['key'] . '>' . ($this->regex[$matches['key']] ?? $this->defaultRegex) . ')';
            },
            $path
        );
    }

    protected function attributes($matches)
    {
        return array_filter($matches + $this->defaults, function ($value, $key)
        {
            return !is_int($key) && (is_numeric($value) || !empty($value));
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function test($uri, $method)
    {
        if (!empty($this->methods) && !in_array($method, $this->methods, true))
        {
            return false;
        }

        $result = preg_match('~^' . $this->regexPath . '$~u', $uri, $matches);

        if ($result)
        {
            $this->attributes = $this->attributes($matches);
        }

        return $result !== 0;
    }

    protected function regexUri($http, $path)
    {
        return $http['scheme'] . '\:\/{2}' . $http['domain'] . $path;
    }

    /**
     * @return string
     */
    protected function regex()
    {
        $this->filterPath = $this->pathFilter();

        $regex = $this->toRegex($this->filterPath);
        $http  = $this->http;

        if (!$this->http['scheme'])
        {
            $http['scheme'] = 'https?';
        }

        if (!$this->http['domain'])
        {
            $http['domain'] = '[^\/]+';
        }

        return $this->regexUri($http, $regex);
    }

    /**
     * reload route
     */
    protected function reload()
    {
        $this->defaults  = $this->slice->atData('defaults', []);
        $this->methods   = $this->slice->atData('methods', []);
        $this->regex     = $this->slice->atData('regex', []);
        $this->http      = $this->slice->atData('http', []);
        $this->path      = $this->slice->atData('path');
        $this->regexPath = $this->regex();
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return serialize([
            'defaultRegex' => $this->defaultRegex,
            'http'         => $this->http,
            'path'         => $this->path,
            'regex'        => $this->regex,
            'regexPath'    => $this->regexPath,
            'filterPath'   => $this->filterPath,
            'defaults'     => $this->defaults,
            'methods'      => $this->methods,
            'attributes'   => $this->attributes,
            'slice'        => $this->slice,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized, []);

        foreach ($data as $variable => $value)
        {
            $this->{$variable} = $value;
        }
    }


}
