<?php

namespace Deimos\Router;

use Deimos\Route\Route as ClassRoute;

class Router
{

    /**
     * @var ClassRoute[]
     */
    protected $routes = [];

    /**
     * @var Route[]
     */
    protected $selfRoutes;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $method;

    /**
     * @param ClassRoute $route
     */
    public function addRoute(ClassRoute $route)
    {
        $this->routes[] = $route;
    }
    
    /**
     * @param ClassRoute[] $routes
     *
     * @throws \InvalidArgumentException
     */
    public function setRoutes(array $routes)
    {
        foreach ($routes as $route)
        {
            $classRoute = new ClassRoute($route);
            $this->addRoute($classRoute);
        }
    }

    /**
     * @param $path
     */
    protected function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @param string $method
     */
    public function setMethod($method = 'GET')
    {
        $this->method = $method;
    }

    /**
     * @param string $path
     *
     * @return Route
     */
    public function getCurrentRoute($path)
    {
        $this->setPath($path);

        if (!isset($this->selfRoutes[$this->path]))
        {
            $this->selfRoutes[$this->path] = $this->run();
        }

        return $this->selfRoutes[$this->path];
    }

    /**
     * @param string $rulePath
     *
     * @return mixed
     */
    protected function optional($rulePath)
    {
        return preg_replace('~\)~', ')?', $rulePath);
    }

    /**
     * @param string $test
     *
     * @return array
     */
    protected function test($test)
    {
        preg_match('~^' . $test . '$~', $this->path, $matches);

        return $matches;
    }

    /**
     * @param $data
     *
     * @return string
     */
    protected function val($data)
    {
        if (is_array($data))
        {
            return $data[1];
        }

        return $data;
    }

    /**
     * @param $string
     *
     * @return array
     */
    protected function tokenizer($string)
    {
        $tokens = token_get_all('<?php ' . $string);
        array_shift($tokens);

        $attribute = $this->val(array_shift($tokens));
        if (current($tokens))
        {
            array_shift($tokens);

            foreach ($tokens as $key => $token)
            {
                $tokens[$key] = $this->val($token);
            }
        }

        return [$attribute, implode($tokens)];
    }

    /**
     * @param ClassRoute $route
     *
     * @return array
     */
    protected function match($route)
    {
        if (!$route->methodIsAllow($this->method))
        {
            return [];
        }

        $path = $this->optional($route->route());
        $path = preg_replace_callback('~\<(.*?)\>~u', function ($matches) use (&$route)
        {
            list ($match, $newRegExp) = $this->tokenizer($matches[1]);
            $defaultAttributes[$match] = null;

            if (empty($newRegExp))
            {
                $newRegExp = $route->regExp($match);
            }

            return '(?<' . $match . '>' . $newRegExp . ')';

        }, $path);

        return $this->test($path);
    }

    /**
     * @return Route
     *
     * @throws \InvalidArgumentException
     */
    protected function run()
    {
        if (!$this->method)
        {
            throw new \InvalidArgumentException('The HTTP method isn\'t known! Use method setMethod($path)!');
        }

        foreach ($this->routes as $route)
        {
            $attributes = $this->match($route);

            if (!empty($attributes))
            {
                return new Route($route, $attributes);
            }
        }

        throw new \InvalidArgumentException('Route `' . $this->path . '` not found');
    }

}