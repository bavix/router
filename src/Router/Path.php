<?php

namespace Bavix\Router;

use Bavix\Exceptions\Runtime;

class Path
{

    /**
     * default regexp
     */
    protected const DEFAULT_REGEX = '[\w-]+';

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var array
     */
    protected $regex;

    /**
     * Path constructor.
     *
     * @param string $value
     * @param array  $regex
     */
    public function __construct(string $value, array $regex = [])
    {
        $this->value = $value;
        $this->regex = $regex;
        $this->processing();
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function quote(string $value): string
    {
        $path = \preg_quote($value, '()');
        $path = \strtr($path, [
            '\\(' => '(',
            '\\)' => ')',
            '\\<' => '<',
            '\\>' => '>',
        ]);

        return \str_replace(')', ')?', $path);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function regexAttribute(string $name): string
    {
        return $this->regex[$name] ?? self::DEFAULT_REGEX;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        if (!$this->pattern) {
            $this->pattern = \preg_replace_callback(
                '~\<(?<key>' . self::DEFAULT_REGEX . '+)\>~',
                function ($matches) {
                    return '(?<' . $matches['key'] . '>' .
                        $this->regexAttribute($matches['key']) .
                        ')';
                },
                $this->quote($this->value)
            );
        }
        return $this->pattern;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getRegex(): array
    {
        return $this->regex;
    }

    /**
     * @param self $parent
     */
    public function hinge(self $parent): void
    {
        $this->pattern = null;
        $this->value   = $parent->value . $this->value;
        $this->regex   = \array_merge(
            $parent->regex,
            $this->regex
        );
    }

    /**
     * processing:
     *  path: '/(<lang:\w+>)' -> '/(<lang>)'
     *  re: [] -> ['lang' => '\w+']
     *
     *  if attr exists -> throws
     */
    protected function processing(): void
    {
        $this->value = \preg_replace_callback(
            '~\<(?<key>' . self::DEFAULT_REGEX . '+):(?<value>.+?)>~',
            function (array $matches) {

                if (!empty($this->regex[$matches['key']])) {
                    throw new Runtime(\sprintf(
                        'duplicate in registry key `%s` for path `%s`',
                        $matches['key'],
                        $this->value
                    ));
                }

                if (!empty($matches['value']))
                {
                    $this->regex[$matches['key']] = $matches['value'];
                }

                return '<' . $matches['key'] . '>';
            },
            $this->value
        );
    }

}
