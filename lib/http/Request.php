<?php

namespace lib\http;

class Request
{
    private RouterPath $route;
    private mixed $params;
    private mixed $body;
    private array $headers;
    private string $method;
    private string $uri;

    private array $middlewareReturns = [];

    public function __construct(RouterPath $route = null, mixed $params = [], mixed $body = [], array $headers = [])
    {
        // get
        parse_str($_SERVER['QUERY_STRING'] ?? '', $queryParams);
        // body
        $httpBody = json_decode(file_get_contents('php://input'), true) ?? [];

        $this->route = $route;
        $this->params = array_merge($params, $queryParams);
        $this->body =  array_merge($body, $httpBody);
        $this->headers = array_merge($headers, getallheaders());
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];
    }

    public function getRoute(): RouterPath
    {
        return $this->route;
    }

    public function addMiddlewareReturn(mixed $return): void
    {
        $this->middlewareReturns[] = $return;
    }

    public function getMiddlewareReturns(): array
    {
        return $this->middlewareReturns;
    }

    public function getParams(): mixed
    {
        return $this->params;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHeader(string $key): string
    {
        return $this->headers[$key] ?? "";
    }

    public function getParam(string $key): mixed
    {
        return $this->params[$key] ?? "";
    }

    public function getBodyParam(string $key): mixed
    {
        return $this->body[$key] ?? "";
    }

    public function getAll(string $key): mixed
    {
        foreach ($this->params as $param) {
            if ($param === $key) {
                return $param;
            }
        }

        foreach ($this->body as $param) {
            if ($param === $key) {
                return $param;
            }
        }

        return null;
    }

    public function has(string $key): bool
    {
        return isset($this->params[$key]) || isset($this->body[$key]);
    }

    public function validate(array $habits): array
    {
        foreach ($habits as $key => $rules) {
            if (!is_array($rules)) {
                $rules = explode('|', $rules);
            }

            if (!in_array('required', $rules) && !$this->has($key)) {
                continue;
            }

            $value = $this->getAll($key) ?? null;

            foreach ($rules as $rule) {
                if ($rule === 'required' && !$this->has($key)) {
                    Response::withError("$key is required")->throw();
                }

                if ($rule === 'integer' && !is_int($value)) {
                    Response::withError("$key must be an integer")->throw();
                }

                if ($rule === 'string' && !is_string($value)) {
                    Response::withError("$key must be a string")->throw();
                }

                if ($rule === 'bool' && !is_bool($value)) {
                    Response::withError("$key must be a boolean")->throw();
                }

                if ($rule === 'array' && !is_array($value)) {
                    Response::withError("$key must be an array")->throw();
                }
            }
        }

        return array_map([$this, 'getAll'], array_keys($habits));
    }
}
