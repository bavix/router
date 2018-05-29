<?php

namespace Bavix\Router;

class Build
{

    /**
     * @param string $path
     * @param null|string $host
     * @param null|string $protocol
     * @return string
     */
    public static function url(string $path, ?string $host = null, ?string $protocol = null): string
    {
        $scheme = $protocol ?? Server::sharedInstance()->protocol();
        $host = $host ?? Server::sharedInstance()->host();
        return $scheme . '://' . $host . $path;
    }

}
