<?php

$service = '';
require_once __DIR__.'/../app/bootstrap.php';

use Domain\Node;

$node = new Node([
    'ip' => '127.0.0.1',
    'port' => '8002',
    'active' => true,
    'last_active_at' => 0,
]);

$ndt = new \App\Classes\NodeDataTransfer($node);

$ndt->ping();

