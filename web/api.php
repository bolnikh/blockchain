<?php

// $_ENV['env_file'] = '.env';

require_once __DIR__ . '\..\app\bootstrap.php';

$router = new App\Classes\RouterApi();
$router->run();
