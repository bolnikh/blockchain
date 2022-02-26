<?php

$service = '';
require_once __DIR__.'/../app/bootstrap.php';


$pana = new \App\Actions\PingAllNodesAction();
$pana->run();
