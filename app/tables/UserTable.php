<?php

namespace app\tables;

use lib\database\Blueprint;
use lib\database\Database;
use lib\database\Table;

// use app\models\User;

class UserTable extends Database implements Table
{
    public static function up()
    {
        self::create('users', function (Blueprint $table) {
            $table->id();
            // your columns here
            $table->timestamps();
        });
    }

    public static function seed()
    {
        // User::create([]);
    }

    public static function down()
    {
        self::drop('users');
    }
}
