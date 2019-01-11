<?php

namespace Bavix\Router;

interface GroupResolution
{

    /**
     * @param array $methods
     * @param string $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function methods(array $methods, string $path, ?string $name): Pattern;

    /**
     * @param string $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function any(string $path, ?string $name = null): Pattern;

    /**
     * @param string $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function get(string $path, ?string $name = null): Pattern;

    /**
     * @param string $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function post(string $path, ?string $name = null): Pattern;

    /**
     * @param string $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function put(string $path, ?string $name = null): Pattern;

    /**
     * @param string $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function patch(string $path, ?string $name = null): Pattern;

    /**
     * @param string $path
     * @param null|string $name
     *
     * @return Pattern
     */
    public function delete(string $path, ?string $name = null): Pattern;

    /**
     * @param string $entityName
     * @param null|string $name
     * @return Pattern
     */
    public function index(string $entityName, ?string $name = null): Pattern;

    /**
     * @param string $entityName
     * @param null|string $name
     * @return Pattern
     */
    public function create(string $entityName, ?string $name = null): Pattern;

    /**
     * @param string $entityName
     * @param null|string $name
     * @return Pattern
     */
    public function store(string $entityName, ?string $name = null): Pattern;

    /**
     * @param string $entityName
     * @param null|string $name
     * @param null|string $id
     * @return Pattern
     */
    public function show(string $entityName, ?string $name = null, ?string $id = null): Pattern;

    /**
     * @param string $entityName
     * @param null|string $name
     * @param null|string $id
     * @return Pattern
     */
    public function edit(string $entityName, ?string $name = null, ?string $id = null): Pattern;

    /**
     * @param string $entityName
     * @param null|string $name
     * @param null|string $id
     * @return Pattern
     */
    public function update(string $entityName, ?string $name = null, ?string $id = null): Pattern;

    /**
     * @param string $entityName
     * @param null|string $name
     * @param null|string $id
     * @return Pattern
     */
    public function destroy(string $entityName, ?string $name = null, ?string $id = null): Pattern;

    /**
     * @param string $entityName
     * @param null|string $name
     * @param null|string $id
     *
     * @return ResourceCollection
     */
    public function resource(string $entityName, ?string $name = null, ?string $id = null): ResourceCollection;

    /**
     * @param string $entityName
     * @param null|string $name
     * @param null|string $id
     *
     * @return ResourceCollection
     */
    public function apiResource(string $entityName, ?string $name = null, ?string $id = null): ResourceCollection;

}
