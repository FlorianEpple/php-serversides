<?php

namespace lib\artisan\commands;

use lib\artisan\Artisan;

class UtilityCmds
{
    public static function register()
    {
        Artisan::command("serve", [self::class, "serve"]);
    }

    public static function serve($argv)
    {
        $port = 8000;

        if (count($argv) == 2) {
            $port = $argv[1];

            if (strlen($port) > 4) {
                Artisan::error("Invalid port number");
                return;
            }
            if (strlen($port) == 1) {
                $port = str_repeat($port, 4);
            } elseif (strlen($port) == 2) {
                $port = str_repeat($port, 2);
            } elseif (strlen($port) == 3) {
                $port = $port . "0";
            }
        }

        Artisan::info("Starting development server at http://localhost:$port");
        Artisan::exec("php -S localhost:$port -t public");
    }
}
