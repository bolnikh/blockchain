<?php


use App\Actions\CreateNewBlockAction;
use App\Actions\DeleteExpiredTrxAction;
use App\Actions\LoadAndReplaceBlocksAction;
use App\Actions\LoadTrxAction;
use App\Actions\PingAllNodesAction;
use App\Actions\SendAllTrxAction;
use App\Actions\SendNewBlockAction;
use Domain\TrxNew;

require_once __DIR__.'/../app/bootstrap.php';

$service = \App\Classes\ServiceLocator::instance();

/**
 * @var App\Classes\Config $config
 */
$config = $service->get('Config');

/**
 * @var \Domain\Interfaces\BlockChainStorageInterface $blockChainStorage
 */
$blockChainStorage = $service->get('BlockChainStorage');

/**
 * @var \Domain\Interfaces\TrxStorageInterface $trxStorage
 */
$trxStorage = $service->get('TrxStorage');

/**
 * @var \Domain\Interfaces\NodeStorageInterface $nodeStorage
 */
$nodeStorage = $service->get('NodeStorage');


while (true)
{
    (new PingAllNodesAction())->run();
    (new DeleteExpiredTrxAction())->run();


    $tnx = null;
    // generate trnx
    if (mt_rand(0, 10) < 5) {
       // check if positive node key balance - and send it to random key

        $nodeBalance = $blockChainStorage->balance($config->node_public_key);
        if ($nodeBalance > 100) {
            $fileName = STORAGE_DIR.'/keys/key'.mt_rand(1, 5).'.pem';
            $km_to = new \Domain\KeyMaster(file_get_contents($fileName));

            $tnx = new TrxNew([
                'private_key' => $config->node_private_key,
                'to' => $km_to->getPublicKey(true),
                'amount' => mt_rand(5, 20),
            ]);

            $trxStorage->store($tnx);
        }
    }

    (new SendAllTrxAction())->run();

    // load trnx
    (new LoadTrxAction())->run();


    // load and replace blockchain from other nodes
    (new LoadAndReplaceBlocksAction())->run();

    // create new block using trnx
    (new CreateNewBlockAction())->run();

    // send new block to other nodes
    (new SendNewBlockAction())->run();


    echo 'sleep'."\n";
    sleep(10);
}
