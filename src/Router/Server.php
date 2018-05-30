<?php

namespace Bavix\Router;

class Server
{

    /**
     * @var array
     */
    protected $input = [];

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
    public function get(string $name, ?string $default = null): ?string
    {
        if (empty($this->input[$name])) {
            $this->input[$name] = \filter_input(INPUT_SERVER, $name, \FILTER_DEFAULT, [
                'options' => [
                    'default' => $default
                ]
            ]);
        }
        return $this->input[$name];
    }

    /**
     * @param string $default
     *
     * @return string
     */
    public function method(string $default = 'GET'): string
    {
        return $this->get('REQUEST_METHOD', $default);
    }

    /**
     * @param string $default
     * @return string
     */
    public function protocol(string $default = 'https'): string
    {
        $protocol = $this->get('HTTP_CF_VISITOR'); // cloudFlare

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
            $this->get(
                'HTTP_X_FORWARDED_PROTO',
                $this->get('REQUEST_SCHEME', $default)
            );
    }

    /**
     * @return string
     */
    public function host(): string
    {
        return $this->get('HTTP_HOST', PHP_SAPI);
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return \parse_url($this->get('REQUEST_URI'), PHP_URL_PATH);
    }

    /**
     * @param string $path
     * @param null|string $host
     * @param null|string $protocol
     * @return string
     */
    public static function url(string $path, ?string $host = null, ?string $protocol = null): string
    {
        $scheme = $protocol ?? static::sharedInstance()->protocol();
        $host = $host ?? static::sharedInstance()->host();
        return $scheme . '://' . $host . $path;
    }

}
