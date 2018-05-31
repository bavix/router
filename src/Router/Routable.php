<?php

namespace Bavix\Router;

interface Routable extends \Serializable, \JsonSerializable
{

    /**
     * @param Match $match
     */
    public function __construct(Match $match);

    /**
     * @return string
     */
    public function getProtocol(): string;

    /**
     * @return string
     */
    public function getHost(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @return string
     */
    public function getPathValue(): string;

    /**
     * @return string
     */
    public function getPathPattern(): string;

    /**
     * @return string
     */
    public function getPattern(): string;

    /**
     * @return array
     */
    public function getAttributes(): array;

    /**
     * @return array
     */
    public function getDefaults(): array;

    /**
     * @return array
     */
    public function getGroups(): array;

    /**
     * @return null|array
     */
    public function getMethods(): ?array;

}
