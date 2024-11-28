<?php

namespace lib\routine;

class Routines
{
    /** @var Routine[] $routines */
    private static array $routines = [];

    public static function appendRoutine(string $name, callable $fnc): void
    {
        self::$routines[] = new Routine($name, $fnc);
    }

    public static function runAllRoutines(): void
    {
        foreach (self::$routines as $routine) {
            if (is_callable($routine->fnc))
                call_user_func($routine->fnc);
        }
    }

    public static function getRoutines(): array
    {
        return self::$routines;
    }
}
