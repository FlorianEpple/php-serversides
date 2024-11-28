<?php

namespace lib\artisan\commands;

use lib\artisan\Artisan;
use lib\Env;

class DatabaseCmds
{
    public static function register()
    {
        $mysql_user = Env::get("MYSQL_USERNAME");

        // Register migrate command
        Artisan::shortcut("", "mysql -u $mysql_user");
        // Register migrate command
        Artisan::command("migrate", [self::class, "migrate"]);
        // Register seed command
        Artisan::command("seed", [self::class, "seed"]);
        // Register refresh command
        Artisan::command("refresh", [self::class, "refresh"]);
        // Register rollback command
        Artisan::command("rollback", [self::class, "rollback"]);
    }

    private static function getTables(): array
    {
        $tables = [];
        $dir = __DIR__ . '/../../../app/tables/';
        $files = scandir($dir);

        foreach ($files as $file) {
            if (preg_match('/^(.*)\.php$/', $file, $matches)) {
                $tables[] = $matches[1];
            }
        }

        return $tables;
    }

    public static function migrate($args)
    {
        foreach (self::getTables() as $table) {
            $table_class = "app\\tables\\" . $table;
            Artisan::info("Migrating $table");
            $table_class::up();
        }

        Artisan::success("Migration complete");
    }

    public static function seed($args)
    {
        foreach (self::getTables() as $table) {
            $table_class = "app\\tables\\" . $table;
            Artisan::info("Seeding $table");
            $table_class::seed();
        }

        Artisan::success("Seeding complete");
    }

    public static function refresh($args)
    {
        self::rollback($args);
        self::migrate($args);
    }

    public static function rollback($args)
    {
        foreach (self::getTables() as $table) {
            $table_class = "app\\tables\\" . $table;
            Artisan::info("Rolling back $table");
            $table_class::down();
        }

        Artisan::success("Rollback complete");
    }
}
