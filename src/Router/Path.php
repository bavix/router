<?php

namespace Bavix\Router;

use Bavix\Exceptions\Runtime;

class Path
{

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $regex;

    /**
     * Path constructor.
     * @param string $path
     * @param array $regex
     */
    public function __construct(string $path, array $regex = [])
    {
        $this->path = $path;
        $this->regex = $regex;
        $this->processing();
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        // todo
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
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
        $this->path = $parent->path . $this->path;
        $this->regex = \array_merge(
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
        $this->path = \preg_replace_callback(
            '~\<(?<key>\w+):(?<value>.+?)>~',
            function (array $matches) {

                if (!empty($this->regex[$matches['key']])) {
                    throw new Runtime(\sprintf(
                        'duplicate in registry key `%s` for path `%s`',
                        $matches['key'],
                        $this->path
                    ));
                }

                if (!empty($matches['value']))
                {
                    $this->regex[$matches['key']] = $matches['value'];
                }

                return '<' . $matches['key'] . '>';
            },
            $this->path
        );
    }

}
