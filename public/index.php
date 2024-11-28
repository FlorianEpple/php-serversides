<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../router/api.php';
require_once __DIR__ . '/../router/routines.php';

use lib\http\RouteProvider;
use lib\http\Router;

header("Access-Control-Allow-Origin: http://localhost:3000");
// header("Content-Type: application/json; charset=UTF-8");
header("Content-Type: text/text; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-Auth-Key");
header("Access-Control-Allow-Credentials: true");

RouteProvider::workWith(Router::class);
