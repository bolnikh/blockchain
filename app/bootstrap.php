<?php

const ROOT_DIR = __DIR__ . '/../';

require_once ROOT_DIR . '/vendor/autoload.php';

$config = new App\Classes\Config();
$service = new App\Classes\ServiceLocator();

$service->addInstance('config', $config);
$service->addInstance('storage', new \Domain\Storages\BlockChainStorageFile());



function dd($var)
{
    var_dump($var);
    die();
}