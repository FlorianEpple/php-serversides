<?php

namespace lib\http;

class Router
{
    protected static array $paths = [];
    protected static array $prefixes = [];
    protected static array $middlewares = [];

    public function __construct()
    {
        Response::withError('Router class cannot be instantiated')->throw();
    }

    private static function addRoute(string $path, RouteMethod $method, array|callable $callback, array|callable $middleware = null)
    {
        if (strlen($path) > 0 && $path[0] === '/') {
            $path = substr($path, 1);
        }

        if (count(self::$prefixes) > 0) {
            $fullPath = '/' . join('/', self::$prefixes) . '/' . $path;
        } else {
            $fullPath = '/' . $path;
        }

        if (strlen($fullPath) > 0 && $fullPath[strlen($fullPath) - 1] === '/') {
            $fullPath = substr($fullPath, 0, strlen($fullPath) - 1);
        }

        $fullMiddlewares = [];


        // middleware

        // - function () {}
        // - ["namespace::class", "function"]
        // - [["namespace::class", "function"], ["namespace::class", "function"]]

        // grouped middlewares
        foreach (self::$middlewares as $item) {
            if (is_array($item) && count($item) > 0 && is_array($item[0])) {
                foreach ($item as $inner) {
                    $fullMiddlewares[] = $inner;
                }
            } else if (is_array($item) && count($item) > 0) {
                $fullMiddlewares[] = $item;
            } else if ($middleware !== null) {
                $fullMiddlewares[] = $item;
            }
        }

        // single middlewares
        if (is_array($middleware) && count($middleware) > 0 && is_array($middleware[0])) {
            foreach ($middleware as $inner) {
                $fullMiddlewares[] = $inner;
            }
        } else if (is_array($middleware) && count($middleware) > 0) {
            $fullMiddlewares[] = $middleware;
        } else if ($middleware !== null) {
            $fullMiddlewares[] = $middleware;
        }

        $routerPath = new RouterPath($fullPath, $method, $callback, $fullMiddlewares);
        self::$paths[] = $routerPath;
    }

    public static function get(string $path, array|callable $callback, array|callable $middleware = null): void
    {
        self::addRoute($path, RouteMethod::GET, $callback, $middleware);
    }

    public static function post(string $path, array|callable $callback, array|callable $middleware = null): void
    {
        self::addRoute($path, RouteMethod::POST, $callback, $middleware);
    }

    public static function put(string $path, array|callable $callback, array|callable $middleware = null): void
    {
        self::addRoute($path, RouteMethod::PUT, $callback, $middleware);
    }

    public static function delete(string $path, array|callable $callback, array|callable $middleware = null): void
    {
        self::addRoute($path, RouteMethod::DELETE, $callback, $middleware);
    }

    public static function options(string $path, array|callable $callback, array|callable $middleware = null): void
    {
        self::addRoute($path, RouteMethod::OPTIONS, $callback, $middleware);
    }

    public static function prefix(string $prefix): RouterGroup
    {
        return new RouterGroup($prefix);
    }

    public static function group(array|callable $callback): void
    {
        $callback();
    }

    // additional routes
    //
    public static function resource(string $path, string $class, array|callable $middleware = null): void
    {
        self::prefix($path)->middleware($middleware ?? [])->group(function () use ($class) {
            // index
            self::get('/', [$class, "index"]);
            // show
            self::get('/{id}', [$class, "show"]);
            // store
            self::post('/', [$class, "store"]);
            // update
            self::put('/{id}', [$class, "update"]);
            // delete
            self::delete('/{id}', [$class, "delete"]);
        });
    }

    public static function ping()
    {
        self::get('/ping', function () {
            return Response::withMessage("It Works!");
        });
    }

    // END
    //

    public static function getRoutes(): array
    {
        return self::$paths;
    }

    public static function addPrefix(string $prefix): int
    {
        if ($prefix[0] === '/') {
            $prefix = substr($prefix, 1);
        }

        self::$prefixes[] = $prefix;
        return count(self::$prefixes) - 1;
    }

    public static function removePrefix(int $index): void
    {
        array_splice(self::$prefixes, $index, 1);
    }

    public static function addMiddlewares(array|callable $middlewares): int
    {
        self::$middlewares[] = $middlewares;

        return count(self::$middlewares) - 1;
    }

    public static function removeMiddlewares(int $index): void
    {
        array_splice(self::$middlewares, $index, 1);
    }
}
