<?php

$_ENV['env_file'] = '.env1';

require_once __DIR__ . '\..\app\bootstrap.php';

$router = new App\Classes\RouterApi();
$router->run();
