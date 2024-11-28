<?php

namespace lib\http;

use Error;
use Exception;
use lib\routine\Routines;

class RouteProvider
{
    private function __construct()
    {
        throw new Error("RouteProvider is a no children class");
    }

    private static function requestedUri(): string
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            return  parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }

        return '';
    }

    private static function requestedMethod(): RouteMethod
    {
        $requestMethodString = $_SERVER['REQUEST_METHOD'] ?? "";
        $requestMethod = RouteMethod::GET;

        switch ($requestMethodString) {
            case "POST":
                $requestMethod = RouteMethod::POST;
                break;
            case "PUT":
                $requestMethod = RouteMethod::PUT;
                break;
            case "DELETE":
                $requestMethod = RouteMethod::DELETE;
                break;

            default:
                break;
        }

        return $requestMethod;
    }

    private static function parseUri(string $uri): array
    {
        // Remove leading slashes
        if (strlen($uri) > 0 && $uri[0] === '/') {
            $uri = substr($uri, 1);
        }

        // Remove trailing slashes
        if (strlen($uri) > 0 && $uri[strlen($uri) - 1] === '/') {
            $uri = substr($uri, 0, strlen($uri) - 1);
        }

        return explode("/", $uri);
    }

    private static function getRequestedPath(): ?RouterPath
    {
        $uri = self::requestedUri();
        $requestMethod = self::requestedMethod();
        $uriParts = self::parseUri($uri);

        foreach (Router::getRoutes() as $route) {
            $routePathParts = self::parseUri($route->path);

            // if route method is not equal to the requested method
            if (!$route->method === $requestMethod) {
                continue;
            }

            // go through all path parts of current route
            for ($i = 0; $i < count($routePathParts); $i++) {
                // if route path > requested path
                if ($i >= count($uriParts)) {
                    // continue with next route
                    break;
                }

                if (preg_match('/^\{(.+?)\}$/', $routePathParts[$i], $matches)) {
                    // if router path at $i is a route param (e.g. '{id}')
                    //
                    // add the part param to the list of route params
                    $route->addParam($matches[1], $uriParts[$i]);
                } else if ($routePathParts[$i] !== $uriParts[$i]) {
                    // if the path do not match!
                    // and router path at $i is a normal path (e.g. '/dir/')
                    //
                    // continue with next route
                    break;
                }
            }

            // if requested path is longer than the route
            if ($i < count($uriParts)) {
                // continue with next route
                continue;
            }

            return $route;
        }

        return null;
    }

    public static function workWith(mixed $router): void
    {
        try {
            $route = self::getRequestedPath();

            if ($route === null) {
                Response::withError('Route not found', new ResponseError(404, "Not Found", [
                    "message" => "Route not found"
                ]))->throw();
            }

            // get the route params (e.g. /{id}/)
            $params = $route->getParams();
            $request = new Request($route, $params);

            // middlewares
            foreach ($route->getMiddlewares() as $middleware) {
                // run the middleware                
                if (is_callable($middleware)) {
                    // run the middleware with $request
                    // add the return to the request
                    $request->addMiddlewareReturn($middleware($request) ?? []);
                }
            }

            // controller
            if (is_callable($route->getCallback())) {
                $response = $route->getCallback()($request);

                if (!($response instanceof Response)) {
                    // if is no instanceof Result
                    Response::json([], 'Success Response')->throw();
                } else if (!method_exists($response, 'throw')) {
                    // if member method 'throw' does not exist
                    Response::json([], 'Success Response')->throw();
                } else {
                    // if member method 'throw' does exist
                    $response->throw();
                }
            }
        } catch (Error $e) {
            // Handle any unexpected exceptions
            Response::withError($e->getMessage(), new ResponseError(500, "Internal Server Error", [
                "message" => $e->getMessage()
            ]))->throw();
        }
    }
}
