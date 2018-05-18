<?php

namespace Bavix\Router;

use Bavix\Slice\Slice;

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
     * @param Slice $data
     * @param string $defaultRegex
     */
    public function __construct($data, string $defaultRegex = null)
    {
        $this->slice = Slice::from($data);

        if ($defaultRegex !== null)
        {
            $this->defaultRegex = $defaultRegex;
        }

        $this->reload();
    }

    /**
     * @return array
     */
    public function getHttp(): array
    {
        return $this->http;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getRegex(): array
    {
        return $this->regex;
    }

    /**
     * @return string
     */
    public function getRegexPath(): string
    {
        return $this->regexPath;
    }

    /**
     * @return string
     */
    public function getFilterPath(): string
    {
        return $this->filterPath;
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        return (array)$this->defaults;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes ?? $this->defaults;
    }

    /**
     * @return string
     */
    protected function pathFilter(): string
    {
        return \preg_replace_callback(
            '~\<(?<key>\w+)(\:(?<value>.+?))?\>~',
            function ($matches) {
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
    protected function quote(string $path): string
    {
        $path = \preg_quote($path, '()');
        $path = \strtr($path, [
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
    protected function optional(string $rulePath): string
    {
        return \str_replace(')', ')?', $rulePath);
    }

    /**
     * @param string $route
     *
     * @return string
     */
    protected function toRegex(string $route): string
    {
        $path = $this->quote($route);

        return \preg_replace_callback(
            '~\<(?<key>[\w-]+)\>~',
            function ($matches) {
                return '(?<' . $matches['key'] . '>' . ($this->regex[$matches['key']] ?? $this->defaultRegex) . ')';
            },
            $path
        );
    }

    /**
     * @param string[] $matches
     *
     * @return array
     */
    protected function attributes(array $matches): array
    {
        return \array_filter($matches, function ($value, $key) {
            return !\is_int($key) && (\is_numeric($value) || !empty($value));
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    public function methodValid(?string $method): bool
    {
        return empty($this->methods) ||
            \in_array($method, $this->methods, true) ||
            ($method === 'AJAX' && isAjax());
    }

    /**
     * @param string $uri
     *
     * @return bool
     */
    public function uriValid(string $uri): bool
    {
        $result = preg_match('~^' . $this->regexPath . '$~u', $uri, $matches);

        if ($result)
        {
            $this->attributes = \array_merge($this->defaults, $this->attributes($matches));
        }

        return $result !== 0;
    }

    /**
     * @param string $uri
     * @param string $method
     *
     * @return bool
     */
    public function test(string $uri, ?string $method): bool
    {
        if (!$this->methodValid($method))
        {
            return false;
        }

        return $this->uriValid($uri);
    }

    /**
     * @param array  $http
     * @param string $path
     *
     * @return string
     */
    protected function regexUri(array $http, string $path): string
    {
        return $http['protocol'] . '\:\/{2}' . $http['host'] . $path;
    }

    /**
     * @return string
     */
    protected function regex(): string
    {
        $this->filterPath = $this->pathFilter();

        $regex = $this->toRegex($this->filterPath);
        $http  = $this->http;

        if (!$this->http['protocol'])
        {
            $http['protocol'] = 'https?';
        }

        if (!$this->http['host'])
        {
            $http['host'] = '[^\/]+';
        }

        return $this->regexUri($http, $regex);
    }

    /**
     * reload route
     */
    protected function reload(): void
    {
        $http = [
            'protocol' => null,
            'host'     => null
        ];

        $this->http      = (array)$this->slice->atData('http', $http);
        $this->defaults  = (array)$this->slice->atData('defaults');
        $this->methods   = (array)$this->slice->atData('methods');
        $this->regex     = (array)$this->slice->atData('regex');
        $this->path      = $this->slice->atData('path');
        $this->regexPath = $this->regex();
    }

    /**
     * @inheritdoc
     */
    public function serialize(): string
    {
        return \serialize([
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
    public function unserialize($serialized): void
    {
        $data = (array)\unserialize($serialized, null);

        foreach ($data as $variable => $value)
        {
            $this->{$variable} = $value;
        }
    }


}
