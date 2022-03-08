<?php


declare(strict_types=1);

namespace Domain\Factory;


use App\Classes\Config;
use App\Classes\ServiceLocator;
use Domain\Interfaces\BlockChainStorageInterface;
use Domain\Interfaces\TrxStorageInterface;
use Domain\KeyMaster;
use Domain\TrxNew;

class TrxNewBalancedFactory
{

    private ServiceLocator $service;
    private Config $config;
    private BlockChainStorageInterface $blockChainStorage;
    private TrxStorageInterface $trxStorage;
    private TrxStorageInterface $newTrxStorage;

    public function __construct()
    {
        $this->service = ServiceLocator::instance();
        $this->config = $this->service->get('Config');
        $this->blockChainStorage = $this->service->get('BlockChainStorage');
        $this->trxStorage = $this->service->get('TrxStorage');
        $this->newTrxStorage = $this->service->get('NewTrxStorage');

    }

    public function get() : TrxNew|null
    {
        $km_from = new KeyMaster($this->config->node_private_key);

        $key_file = STORAGE_DIR.'/keys/key'.mt_rand(1,5).'.pem';
        $km_to = new KeyMaster(file_get_contents($key_file));

        $balance_from = $this->blockChainStorage->balance($km_from->getPublicKey(true));

        if ($balance_from >= 10) {
            $trx = new TrxNew([
                'private_key' => $this->config->node_private_key,
                'to' => $km_to->getPublicKey(true),
                'amount' => mt_rand(1, 10),
            ]);

            return $trx;
        }

        return null;
    }
}