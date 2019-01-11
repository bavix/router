<?php

namespace Bavix\Router;

class Server
{

    protected const PROTOCOL_HTTPS = 'https';

    /**
     * @var array
     */
    protected $input = [];

    /**
     * @param string $path
     * @param null|string $host
     * @param null|string $protocol
     * @return string
     */
    public static function url(string $path, ?string $host = null, ?string $protocol = null): string
    {
        $scheme = $protocol ?: static::sharedInstance()->protocol();
        $host = $host ?? static::sharedInstance()->host();
        return $scheme . '://' . $host . $path;
    }

    /**
     * @param string $default
     * @return string
     */
    public function protocol(string $default = self::PROTOCOL_HTTPS): string
    {
        /**
         * PHP https check with flexible ssl (CloudFlare), how to do?
         *
         * @see https://stackoverflow.com/a/42387790
         */
        $visitor = $this->get('HTTP_CF_VISITOR');
        if ($visitor) {
            $data = \json_decode($visitor, true);
            return $data['scheme'] ?? $default;
        }

        /**
         * PHP Group guidelines
         */
        $https = $this->get('HTTPS', 'off');
        $port = (int)$this->get('SERVER_PORT', 80);
        if ($https === 'on' || $port === 443) {
            return static::PROTOCOL_HTTPS;
        }

        /**
         * Check other popular keys
         */
        return $this->get(
            'HTTP_X_FORWARDED_PROTO',
            $this->get('REQUEST_SCHEME', $default)
        );
    }

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
     * @return string
     */
    public function host(): string
    {
        return $this->get('HTTP_HOST', PHP_SAPI);
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
     * @return string
     */
    public function path(): string
    {
        return \parse_url($this->get('REQUEST_URI'), PHP_URL_PATH);
    }

}
