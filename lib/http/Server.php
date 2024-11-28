<?php

namespace lib\http;

class Server
{
    /**
     * @var array The list of IP addresses considered as localhost
     */
    private static $localhostWhitelist = ['127.0.0.1', '::1'];

    /**
     * Get the client's IP address
     *
     * @return string
     */
    public static function getClientIp(): string
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Check if the request is coming from localhost
     *
     * @return bool
     */
    public static function isLocalhost(): bool
    {
        return in_array(self::getClientIp(), self::$localhostWhitelist);
    }

    /**
     * Get the request method (e.g., GET, POST)
     *
     * @return string
     */
    public static function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Check if the request is an AJAX request
     *
     * @return bool
     */
    public static function isAjax(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    /**
     * @param string $param
     * @param $default
     * @return mixed|null
     */
    public static function getQueryParam(string $param, $default = null): mixed
    {
        return $_GET[$param] ?? $default;
    }

    /**
     * @param string $param
     * @param $default
     * @return mixed|null
     */
    public static function getPostParam(string $param, $default = null): mixed
    {
        return $_POST[$param] ?? $default;
    }

    /**
     * @param string $header
     * @param $default
     * @return mixed|null
     */
    public static function getRequestHeader(string $header, $default = null): mixed
    {
        $header = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        return $_SERVER[$header] ?? $default;
    }
}
