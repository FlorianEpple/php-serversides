<?php

namespace lib\http;

class RouterParam
{
    protected string $key;
    protected string $value;

    /**
     * @param string $key
     * @param string $value
     */
    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return "key: {$this->key}, value: {$this->value}";
    }
}