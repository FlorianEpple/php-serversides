<?php

namespace lib\routine;

class Routine
{
    public string $name;

    /** @var callable $fnc */
    public $fnc;

    /**
     * @param string $name
     * @param callable $fnc
     */
    public function __construct(string $name, callable $fnc)
    {
        $this->name = $name;
        $this->fnc = $fnc;
    }
}
