<?php

require_once __DIR__ . '\..\vendor\autoload.php';

$router = new App\Classes\Router('api');
$router->run();
