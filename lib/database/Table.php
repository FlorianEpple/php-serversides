<?php

namespace lib\database;

interface Table
{
    public static function up();
    public static function seed();
    public static function down();
}
