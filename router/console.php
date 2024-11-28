<?php

use lib\artisan\Artisan;

// Register list command
Artisan::list();

// Register test command
Artisan::test();

// my first artisan command
Artisan::command("my-first-command", function () {
    Artisan::info("Hello from my first command!");
});
