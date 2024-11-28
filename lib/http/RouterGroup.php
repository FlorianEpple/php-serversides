<?php

namespace lib\http;

class RouterGroup
{
    public string $prefix;
    public int $middlewares = 0;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    // optional adding middlewares
    public function middleware(array|callable $middleware)
    {
        $this->middlewares = Router::addMiddlewares($middleware);

        return $this;
    }

    public function group(callable $routes)
    {
        $index = Router::addPrefix($this->prefix);
        $routes();
        Router::removeMiddlewares($this->middlewares);
        Router::removePrefix($index);
    }
}
