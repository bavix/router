<?php

namespace Bavix\Router;

class Resolver implements GroupResolution
{

    /**
     * @var callable
     */
    protected $pusher;

    /**
     * Resolver constructor.
     *
     * @param callable $pusher
     */
    public function __construct(callable $pusher)
    {
        $this->pusher = $pusher;
    }

    /**
     * @param Pattern $pattern
     *
     * @return Pattern
     */
    protected function callback(Pattern $pattern): Pattern
    {
        return \call_user_func($this->pusher, $pattern);
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
        return $this->callback(new Pattern($path, $name))->methods($methods);
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
        return $this->callback(new Pattern($path, $name))->any();
    }

    /**
     * @param string      $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function get(string $path, ?string $name = null): Pattern
    {
        return $this->callback(new Pattern($path, $name))->get();
    }

    /**
     * @param string      $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function post(string $path, ?string $name = null): Pattern
    {
        return $this->callback(new Pattern($path, $name))->post();
    }

    /**
     * @param string      $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function put(string $path, ?string $name = null): Pattern
    {
        return $this->callback(new Pattern($path, $name))->put();
    }

    /**
     * @param string      $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function patch(string $path, ?string $name = null): Pattern
    {
        return $this->callback(new Pattern($path, $name))->patch();
    }

    /**
     * @param string      $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function delete(string $path, ?string $name = null): Pattern
    {
        return $this->callback(new Pattern($path, $name))->delete();
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
     */
    public function resource(string $entityName, ?string $name = null, ?string $id = null): void
    {
        $entityName = \rtrim($entityName, '/');
        $name = $name ?: \ltrim($entityName, '/');
        $id = $id ?: $name;

        $this->get($entityName, $name . '.index');
        $this->get($entityName . '/create', $name . '.create');

        $this->post($entityName, $name . '.store');

        $this->get($entityName . '/<' . $id . '>', $name . '.show');
        $this->get($entityName . '/<' . $id . '>/edit', $name . '.edit');
        $this->methods(
            ['PUT', 'PATCH'],
            $entityName . '/<' . $id . '>/edit',
            $name . '.update'
        );

        $this->delete($entityName . '/<' . $id . '>', $name . '.destroy');
    }

}
