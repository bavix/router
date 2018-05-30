<?php

namespace Bavix\Router;

use Bavix\Router\Rules\PatternRule;

class Match
{

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var PatternRule
     */
    protected $rule;

    /**
     * @var bool
     */
    protected $test;

    /**
     * @param PatternRule $rule
     * @param string $subject
     */
    public function __construct(PatternRule $rule, string $subject)
    {
        $this->rule = $rule;
        $result = \preg_match($this->regex(), $subject, $matches);
        $this->test = $result !== 0;
        $this->attributes = \array_filter(
            $matches,
            '\is_string',
            \ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @return string
     */
    protected function regex(): string
    {
        $url = Build::url(
            $this->rule->getPath()->getPattern(),
            $this->rule->getHost(),
            $this->rule->getProtocol()
        );

        return '~^' . $url . '$~u';
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return bool
     */
    public function isTest(): bool
    {
        return $this->test;
    }

}
