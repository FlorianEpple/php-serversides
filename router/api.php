<?php

use lib\http\Router;

use app\http\controller\UserController;

// at /ping
Router::ping();

// User resource
// get all      at GET:    /user
// get by id    at GET:    /user/{id}
// create at    at POST:   /user
// update at    at PUT:    /user/{id}
// delete at    at DELETE: /user/{id}
Router::resource("user", UserController::class);