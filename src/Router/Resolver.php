<?php

namespace Bavix\Router;

class Resolver implements GroupResolution
{

    /**
     * @var callable
     */
    protected $collections;

    /**
     * @var callable
     */
    protected $patterns;

    /**
     * Resolver constructor.
     *
     * @param callable $patterns
     * @param callable $collections
     */
    public function __construct(callable $patterns, callable $collections)
    {
        $this->collections = $collections;
        $this->patterns = $patterns;
    }

    /**
     * @param ResourceCollection $collection
     *
     * @return Pattern
     */
    protected function pushCollection(ResourceCollection $collection): ResourceCollection
    {
        return \call_user_func($this->collections, $collection);
    }

    /**
     * @param Pattern $pattern
     *
     * @return Pattern
     */
    protected function pushPattern(Pattern $pattern): Pattern
    {
        return \call_user_func($this->patterns, $pattern);
    }

    /**
     * @param string $path
     * @param null|string $name
     * @return Pattern
     */
    protected function pattern(string $path, ?string $name): Pattern
    {
        return new Pattern($path, $name);
    }

    /**
     * @param array       $methods
     * @param string      $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function methods(array $methods, string $path, ?string $name): Pattern
    {
        return $this->pushPattern($this->pattern($path, $name))->methods($methods);
    }

    /**
     * GET|POST|PUT|PATCH|HEAD|OPTIONS|DELETE
     *
     * @param string      $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function any(string $path, ?string $name = null): Pattern
    {
        return $this->pushPattern($this->pattern($path, $name))->any();
    }

    /**
     * @param string      $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function get(string $path, ?string $name = null): Pattern
    {
        return $this->pushPattern($this->pattern($path, $name))->get();
    }

    /**
     * @param string      $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function post(string $path, ?string $name = null): Pattern
    {
        return $this->pushPattern($this->pattern($path, $name))->post();
    }

    /**
     * @param string      $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function put(string $path, ?string $name = null): Pattern
    {
        return $this->pushPattern($this->pattern($path, $name))->put();
    }

    /**
     * @param string      $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function patch(string $path, ?string $name = null): Pattern
    {
        return $this->pushPattern($this->pattern($path, $name))->patch();
    }

    /**
     * @param string      $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function delete(string $path, ?string $name = null): Pattern
    {
        return $this->pushPattern($this->pattern($path, $name))->delete();
    }

    /**
     * @param ResourceCollection $collection
     * @param string $name
     * @param string $type
     * @return Pattern
     */
    protected function route(ResourceCollection $collection, string $name, string $type): Pattern
    {
        return $collection[$type] = $this->pattern('', $name . '.' . $type);
    }

    /**
     * entityName -> /users
     *
     *  GET         users.index     /users
     *  GET         users.create    /users/create
     *  POST        users.store     /users
     *  GET         users.show      /users/{id}
     *  GET         users.edit      /users/{id}/edit
     *  PUT/PATCH   users.update    /users/{id}/edit
     *  DELETE      users.destroy   /users/{id}
     *
     * @param string      $entityName
     * @param null|string $name
     * @param null|string $id
     *
     * @return ResourceCollection
     */
    public function resource(string $entityName, ?string $name = null, ?string $id = null): ResourceCollection
    {
        $entityName = \rtrim($entityName, '/');
        $name = $name ?: \ltrim($entityName, '/');
        $id = $id ?: $name;

        $collection = new ResourceCollection();
        $this->route($collection, $name, 'index')
            ->setPath($entityName)
            ->get();

        $this->route($collection, $name, 'create')
            ->setPath($entityName . '/create')
            ->get();

        $this->route($collection, $name, 'store')
            ->setPath($entityName)
            ->post();

        $this->route($collection, $name, 'show')
            ->setPath($entityName . '/<' . $id . '>')
            ->get();

        $this->route($collection, $name, 'edit')
            ->setPath($entityName . '/<' . $id . '>/edit')
            ->get();

        $this->route($collection, $name, 'update')
            ->setPath($entityName . '/<' . $id . '>/edit')
            ->setMethods(['PUT', 'PATCH']);

        $this->route($collection, $name, 'destroy')
            ->setPath($entityName . '/<' . $id . '>')
            ->delete();

        return $this->pushCollection($collection);
    }

}
