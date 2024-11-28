<?php

use lib\artisan\Artisan;
use lib\artisan\commands\UtilityCmds;
use lib\artisan\commands\MakeCmds;
use lib\artisan\commands\DatabaseCmds;

// Register serve
Artisan::group("", [UtilityCmds::class, "register"]);

// Register make group
Artisan::group("make", [MakeCmds::class, "register"]);

// Register db group
Artisan::group("db", [DatabaseCmds::class, "register"]);
