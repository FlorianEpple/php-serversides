<?php

namespace lib\artisan;

class Artisan
{
    private static array $commands = [];
    private static string $prefix = "";

    public static function run(array $argv)
    {
        $command = $argv[1];
        $args = array_slice($argv, 1);

        if (isset(self::$commands[$command])) {
            if (is_callable(self::$commands[$command])) {
                call_user_func(self::$commands[$command], $args);
            } else {
                $class = self::$commands[$command];
                $class::run($args);
            }
        } else {
            echo "Command not found\n";
        }
    }

    public static function command(string $command, array|callable $callable)
    {
        if (isset(self::$commands[self::$prefix . $command])) {
            throw new \Exception("Command already exists: " . self::$prefix . $command);
        }

        if ($command == "" && self::$prefix == "") {
            throw new \Exception("Command name cannot be empty");
        }

        if ($command == "") {
            $command = self::$prefix;
        } else if (self::$prefix != "") {
            $command = self::$prefix . ":" . $command;
        }

        self::$commands[$command] = $callable;
    }

    public static function group(string $group, array|callable $callable)
    {
        if ($group != "") {
            self::$prefix = $group;
        }

        $callable();
        self::$prefix = "";
    }

    public static function shortcut(string $shortcut, string $exec)
    {
        self::command($shortcut, function () use ($exec) {
            self::exec($exec);
        });
    }

    public static function exec(string $command)
    {
        echo "> Executing: $command\n\n";

        passthru($command);
    }

    public static function info(string $message)
    {
        echo "\e[1;36m$message\e[0m\n";
    }

    public static function error(string $message)
    {
        echo "\e[1;31m$message\e[0m\n";
    }

    public static function success(string $message)
    {
        echo "\e[1;32m$message\e[0m\n";
    }

    public static function warning(string $message)
    {
        echo "\e[1;33m$message\e[0m\n";
    }

    public static function list()
    {
        self::command("list", function () {
            $groupedCommands = [];
            foreach (self::$commands as $command => $callable) {
                $parts = explode(':', $command);
                $group = $parts[0];
                $groupedCommands[$group][] = $command;
            }

            ksort($groupedCommands);

            echo '------------------' . PHP_EOL;
            echo "Available commands" . PHP_EOL;
            echo '------------------' . PHP_EOL;

            foreach ($groupedCommands as $group => $commands) {
                echo "\n$group:\n";
                foreach ($commands as $command) {
                    echo "  - $command\n";
                }
            }
        });
    }

    public static function test()
    {
        self::command("test", function () {
            echo "It Works!" . PHP_EOL;
        });
    }
}
