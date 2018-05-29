<?php

namespace Bavix\Router;

class RegExp
{

    /**
     * @var null|string
     */
    protected $value;

    /**
     * @var string
     */
    protected $raw;

    /**
     * RegExp constructor.
     *
     * @param string $raw
     * @param null|string $value
     */
    public function __construct(string $raw, ?string $value = null)
    {
        $this->raw = $raw;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function raw(): string
    {
        return $this->raw;
    }

    /**
     * @return null|string
     */
    public function value(): ?string
    {
        return $this->value;
    }

//    public function regularExpression()
//    {
//        \preg_replace_callback(
//            '~\<(?<key>[\w-]+)\>~',
//            function ($matches) {
//                return '(?<' . $matches['key'] . '>' . ($this->regex[$matches['key']] ?? $this->defaultRegex) . ')';
//            },
//            $path
//        );
//    }

    /**
     * @param string $value
     *
     * @return RegExp
     */
    public static function quote(string $value): self
    {
        $path = \preg_quote($value, '()');
        $path = \strtr($path, [
            '\\(' => '(',
            '\\)' => ')',
            '\\<' => '<',
            '\\>' => '>',
        ]);

        return new static(
            \str_replace(')', ')?', $path),
            $value
        );
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)($this->raw ?: $this->value);
    }

}
