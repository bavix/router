<?php

namespace Bavix\Router;

class Server
{

    /**
     * @var array
     */
    protected $server = [];

    /**
     * @return Server
     */
    public static function sharedInstance(): self
    {
        static $self;
        if (!$self) {
            $self = new static();
        }
        return $self;
    }

    /**
     * @param string $name
     * @param null|string $default
     * @return string
     */
    public function server(string $name, ?string $default = null): ?string
    {
        if (empty($this->server[$name])) {
            $this->server[$name] = \filter_input(INPUT_SERVER, $name) ?? $default;
        }
        return $this->server[$name];
    }

    /**
     * @param string $default
     *
     * @return string
     */
    public function method(string $default = 'GET'): string
    {
        return $this->server('REQUEST_METHOD', $default);
    }

    /**
     * @param string $default
     * @return string
     */
    public function protocol(string $default = 'https'): string
    {
        $protocol = $this->server('HTTP_CF_VISITOR'); // cloudFlare

        if ($protocol)
        {
            /**
             * { scheme: "https" }
             *
             * @var string $protocol
             */
            $protocol = json_decode($protocol, true);
        }

        return $protocol['scheme'] ??
            $this->server(
                'HTTP_X_FORWARDED_PROTO',
                $this->server('REQUEST_SCHEME', $default)
            );
    }

    /**
     * @return string
     */
    public function host(): string
    {
        return $this->server('HTTP_HOST', PHP_SAPI);
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return \parse_url($this->server('REQUEST_URI'), PHP_URL_PATH);
    }

}
