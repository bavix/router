<?php

namespace Deimos\Router;

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

function method()
{
    return server('REQUEST_METHOD');
}

function scheme()
{
    $scheme = server('HTTP_CF_VISITOR'); // cloudFlare

    if ($scheme)
    {
        /**
         * { scheme: "https" }
         *
         * @var string $scheme
         */
        $scheme = json_decode($scheme);
    }

    return $scheme['scheme'] ??
        server('HTTP_X_FORWARDED_PROTO') ??
        server('REQUEST_SCHEME');
}

function domain()
{
    return server('HTTP_HOST');
}

function path()
{
    return server('REQUEST_URI');
}
