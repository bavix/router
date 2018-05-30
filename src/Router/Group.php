<?php

namespace Bavix\Router;

use Bavix\Router\Rules\PatternRule;

class Group
{

    /**
     * @var PatternRule[]
     */
    protected $resolver = [];

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $protocol;

    /**
     * @var string
     */
    protected $host;

    /**
     * Group constructor.
     * @param null|string $path
     * @param null|string $name
     */
    public function __construct(?string $path, ?string $name)
    {
        $this->path = $path;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * @param string $protocol
     * @return $this
     */
    public function setProtocol(string $protocol): self
    {
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return $this
     */
    public function setHost(string $host): self
    {
        $this->host = $host;
        return $this;
    }

}
