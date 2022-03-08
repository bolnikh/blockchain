<?php

error_reporting(E_ALL);

use App\Logger\Routes\FileRoute;
use Domain\Storages\BlockChainStorageFile;
use Domain\Storages\TrxStorageFile;
use Domain\Storages\NodeStorageFile;
use App\Classes\ServiceLocator;
use App\Logger\Logger;

const ROOT_DIR = __DIR__ . '/../';

require_once ROOT_DIR . '/vendor/autoload.php';

$service = ServiceLocator::instance();

$service->addInstance('Config', $config = new App\Classes\Config());
define('STORAGE_DIR', ROOT_DIR . $config->storage_dir);

if ($config->storage_type == 'file') {
    $bs = new BlockChainStorageFile(STORAGE_DIR.'/files/blocks/');
    $service->addInstance('BlockChainStorage', $bs);

    $ts = new TrxStorageFile(STORAGE_DIR.'/files/trx/');
    $service->addInstance('TrxStorage', $ts);

    $ns = new NodeStorageFile(STORAGE_DIR.'/files/nodes/');
    $service->addInstance('NodeStorage', $ns);

    $blockChainNewStorage = new BlockChainStorageFile(STORAGE_DIR.'/files/blocks_new/');
    $service->addInstance('BlockChainNewStorage', $blockChainNewStorage);

    $newTrxStorage = new TrxStorageFile(STORAGE_DIR.'/files/trx_new/');
    $service->addInstance('NewTrxStorage', $newTrxStorage);

} else {
    throw new Exception('bad storage_type');
}

$logger = new Logger();
$logger->routes->attach(new FileRoute([
    'isEnable' => true,
    'filePath' => STORAGE_DIR.'/log/default.log',
]));
$service->addInstance('Logger', $logger);






function dd($var)
{
    var_dump($var);
    die();
}