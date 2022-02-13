<?php

use Domain\Storages\BlockChainStorageFile;
use Domain\Storages\TransactionStorageFile;
use Domain\Storages\NodeStorageFile;
use App\Classes\ServiceLocator;

const ROOT_DIR = __DIR__ . '/../';

require_once ROOT_DIR . '/vendor/autoload.php';

$service = ServiceLocator::instance();

$service->addInstance('Config', $config = new App\Classes\Config());
if ($config->storage_type == 'file') {
    $service->addClass('BlockChainStorage', BlockChainStorageFile::class);
    $service->addClass('TrxStorage', TransactionStorageFile::class);
    $service->addClass('NodeStorage', NodeStorageFile::class);
} else {
    throw new Exception('bad storage_type');
}





function dd($var)
{
    var_dump($var);
    die();
}