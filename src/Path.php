<?php

namespace Bavix\Router;

use Bavix\Exceptions\Runtime;

class Path implements \Serializable, \JsonSerializable
{

    /**
     * @var string
     */
    protected $defaultRegex;

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
     * @param string $defaultRegex
     * @param string $value
     * @param array $regex
     */
    public function __construct(string $defaultRegex, string $value, array $regex = [])
    {
        $this->defaultRegex = $defaultRegex;
        $this->value = $value;
        $this->regex = $regex;
        $this->processing();
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
            '~\<(?<key>' . $this->defaultRegex . '+):(?<value>.+?)>~',
            function (array $matches) {

                if (!empty($this->regex[$matches['key']])) {
                    throw new Runtime(\sprintf(
                        'duplicate in registry key `%s` for path `%s`',
                        $matches['key'],
                        $this->value
                    ));
                }

                if (!empty($matches['value'])) {
                    $this->regex[$matches['key']] = $matches['value'];
                }

                return '<' . $matches['key'] . '>';
            },
            $this->value
        );
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        if (!$this->pattern) {
            $this->pattern = \preg_replace_callback(
                '~\<(?<key>' . $this->defaultRegex . '+)\>~',
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
     * @param string $name
     *
     * @return string
     */
    protected function regexAttribute(string $name): string
    {
        return $this->regex[$name] ?? $this->defaultRegex;
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
        $this->value = $parent->value . $this->value;
        $this->regex = \array_merge(
            $parent->regex,
            $this->regex
        );
    }

    /**
     * @return array
     */
    public function __sleep(): array
    {
        return \array_keys($this->jsonSerialize());
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return [
            'defaultRegex' => $this->defaultRegex,
            'pattern' => $this->pattern,
            'regex' => $this->regex,
            'value' => $this->value,
        ];
    }

    /**
     * @inheritdoc
     */
    public function serialize(): string
    {
        return \serialize($this->jsonSerialize());
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized): void
    {
        foreach (\unserialize($serialized, (array)null) as $key => $value) {
            $this->$key = $value;
        }
    }

}
