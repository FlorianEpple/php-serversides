<?php

namespace lib;

class Env
{
    private static string $path = "/../.env";

    public static function exists(string $key, string ...$args): bool
    {
        $env = parse_ini_file(__DIR__ . self::$path);

        if (!isset($env[$key])) {
            return false;
        }

        foreach ($args as $arg) {
            if (!isset($env[$arg])) {
                return false;
            }
        }

        return true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $env = parse_ini_file(__DIR__ . self::$path);
        return $env[$key] ?? $default;
    }
}
