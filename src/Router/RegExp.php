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
    protected $regExp;

    /**
     * RegExp constructor.
     *
     * @param string $regExp
     * @param null|string $value
     */
    public function __construct(string $regExp, ?string $value = null)
    {
        $this->regExp = $regExp;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function regExp(): string
    {
        return $this->regExp;
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

}
