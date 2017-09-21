<?php

namespace Bavix\Router;

/**
 * @param string $variableName
 * @param mixed  $default
 *
 * @return mixed
 */
function server($variableName, $default = null)
{
    static $storage = [];

    if (!isset($storage[$variableName]))
    {
        $storage[$variableName] =
            filter_input(INPUT_SERVER, $variableName) ??
            $default;
    }

    return $storage[$variableName];
}

/**
 * @return string
 */
function method()
{
    return server('REQUEST_METHOD');
}

/**
 * @return string
 */
function protocol()
{
    $protocol = server('HTTP_CF_VISITOR'); // cloudFlare

    if ($protocol)
    {
        /**
         * { scheme: "https" }
         *
         * @var string $protocol
         */
        $protocol = json_decode($protocol);
    }

    return $protocol['scheme'] ??
        server('HTTP_X_FORWARDED_PROTO') ??
        server('REQUEST_SCHEME');
}

/**
 * @return string
 */
function host()
{
    return server('HTTP_HOST');
}

/**
 * @return string
 */
function isAjax()
{
    return server('HTTP_X_REQUESTED_WITH') === 'xmlhttprequest';
}

/**
 * @return string
 */
function path()
{
    return parse_url(server('REQUEST_URI'), PHP_URL_PATH);
}

/**
 * @param Route $route
 * @param array $attributes
 *
 * @return string
 */
function route(Route $route, array $attributes = [])
{
    $attributes += $route->getDefaults();

    $path = preg_replace_callback('~\<(\w+)\>~', function ($matches) use ($attributes)
    {
        return $attributes[$matches[1]] ?? null;
    }, $route->getFilterPath());

    return preg_replace('~(\(/\)|\(|\)|//)~', '', $path);
}
