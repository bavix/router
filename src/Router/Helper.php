<?php

namespace Bavix\Router;

class Helper
{

    /**
     * @param string $path
     *
     * @return string
     */
    public static function generateName(string $path): string
    {
        return \preg_replace(
            '~[^\w-]~',
            '_',
            $path
        );
    }

}
