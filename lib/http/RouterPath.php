<?php

namespace lib\http;

class RouterPath
{
    public string $path;
    public RouteMethod $method;


    //__    
    private mixed $middlewares;
    private mixed $callback;

    private array $params = [];

    public function __construct(string $path, RouteMethod $method, array|callable $callback, array|callable $middlewares = [])
    {
        $this->path = $path;
        $this->method = $method;
        $this->callback = $callback;
        $this->middlewares = $middlewares;
    }

    public function getMiddlewares(): array|callable
    {
        return $this->middlewares;
    }

    public function getCallback(): array|callable
    {
        return $this->callback;
    }

    public function addParam(string $key, string $value): void
    {
        $this->params[$key] = $value;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function __toString()
    {
        return "\"" . $this->path . "\"" .  ' - (' . count($this->middlewares) . ')Middlewares';
    }
}
