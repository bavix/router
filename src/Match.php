<?php

namespace Bavix\Router;

use Bavix\Router\Rules\PatternRule;

class Match implements \Serializable, \JsonSerializable
{

    /**
     * @var array
     */
    protected $urlData;

    /**
     * @var string
     */
    protected $protocol;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $groups = [];

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $subject;

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
     * @param string $method
     */
    public function __construct(PatternRule $rule, string $subject, string $method)
    {
        $this->attributes = $rule->getDefaults();
        $this->urlData = \parse_url($subject);
        $this->rule = $rule;
        $this->method = $method;
        $this->subject = $subject;
        $this->test();
    }

    /**
     * check subject
     */
    protected function test(): void
    {
        if (!$this->methodAllowed()) {
            return;
        }

        $result = \preg_match($this->regex(), $this->subject, $matches);
        $this->test = $result !== 0;
        $this->setGroups(\array_filter(
            $matches,
            '\is_string',
            \ARRAY_FILTER_USE_KEY
        ));
    }

    /**
     * check method
     *
     * @return bool
     */
    protected function methodAllowed(): bool
    {
        $this->test = $this->rule->getMethods() === null ||
            \in_array($this->method, $this->rule->getMethods(), true);

        return $this->isTest();
    }

    /**
     * @return bool
     */
    public function isTest(): bool
    {
        return $this->test;
    }

    /**
     * @return string
     */
    protected function regex(): string
    {
        return '~^' . $this->getPattern() . '$~u';
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return Server::url(
            $this->rule->getPath()->getPattern(),
            $this->rule->getHost(),
            $this->rule->getProtocol()
        );
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     */
    protected function setGroups(array $groups): void
    {
        foreach ($groups as $key => $value) {
            if ($value !== '') {
                $this->groups[$key] = $value;
            }
        }

        $this->attributes = \array_merge(
            $this->attributes,
            $this->groups
        );
    }

    /**
     * @return string
     */
    public function getProtocol(): ?string
    {
        return $this->urlData['scheme'] ?? null;
    }

    /**
     * @return string
     */
    public function getHost(): ?string
    {
        return $this->urlData['host'] ?? null;
    }

    /**
     * @return string
     */
    public function getPath(): ?string
    {
        return $this->urlData['path'] ?? null;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->urlData['query'] ?? null;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return PatternRule
     */
    public function getRule(): PatternRule
    {
        return $this->rule;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
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
            'urlData' => $this->urlData,
            'protocol' => $this->protocol,
            'host' => $this->host,
            'attributes ' => $this->attributes,
            'method' => $this->method,
            'subject' => $this->subject,
            'rule' => $this->rule,
            'test' => $this->test,
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
        $data = \unserialize($serialized, (array)null);
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

}
