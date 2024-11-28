<?php

namespace lib\database;

abstract class Database
{
    public static function create(string $table, callable $builder)
    {
        $blueprint = new Blueprint;

        $builder($blueprint);

        $blueprint->build($table);
    }

    public static function drop(string $table)
    {
        (new MysqlConnection())->executeStatement("DROP TABLE IF EXISTS " . $table . ";", []);
    }
}
