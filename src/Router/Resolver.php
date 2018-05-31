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
     * @param string $name
     * @param string $index
     * @return string
     */
    protected function name(string $name, string $index): string
    {
        return $name . '.' . $index;
    }

    /**
     * @param string $entityName
     * @param string $action
     * @return string
     */
    protected function action(string $entityName, string $action): string
    {
        return $entityName . '/' . $action;
    }

    /**
     * @param string $entityName
     * @param string $id
     * @return string
     */
    protected function id(string $entityName, string $id): string
    {
        return $this->action($entityName, '<' . $id . '>');
    }

    /**
     * @param string $entityName
     * @param string $id
     * @param string $action
     * @return string
     */
    protected function idAction(string $entityName, string $id, string $action): string
    {
        return $this->action($this->id($entityName, $id), $action);
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

        return $this->pushCollection(new ResourceCollection([
            'index' => $this->pattern(
                $entityName,
                $this->name($name, 'index')
            )->get(),

            'create' => $this->pattern(
                $this->action($entityName, 'create'),
                $this->name($name, 'create')
            )->get(),

            'store' => $this->pattern(
                $entityName,
                $this->name($name, 'store')
            )->post(),

            'show' => $this->pattern(
                $this->id($entityName, $id),
                $this->name($name, 'show')
            )->get(),

            'edit' => $this->pattern(
                $this->idAction($entityName, $id, 'edit'),
                $this->name($name, 'edit')
            )->get(),

            'update' => $this->pattern(
                $this->idAction($entityName, $id, 'edit'),
                $this->name($name, 'update')
            )->setMethods(['PUT', 'PATCH']),

            'destroy' => $this->pattern(
                $this->id($entityName, $id),
                $this->name($name, 'destroy')
            )->delete(),
        ]));
    }

}
