<?php

$service = '';
require_once __DIR__.'/../app/bootstrap.php';


$cnb = new \Domain\Actions\CreateNewTrx();
$cnb->run();