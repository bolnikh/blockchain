<?php

declare(strict_types=1);

namespace Domain\Actions;


use App\Classes\ServiceLocator;
use Domain\BlockExists;
use Domain\BlockNew;
use Domain\Interfaces\RunnableInterface;

/**
 * Class CreateNewBlock
 *
 * create new block for blockchain
 * collect trx
 * store them into block
 * produce block
 * store it to blockchain
 *
 * @package Domain\Actions
 */
class CreateNewBlock implements RunnableInterface
{
    private $storage;
    private $config;

    public function __construct(
        private ServiceLocator $service
    )
    {
        $this->storage = $this->service->get('BlockChainStorage');
        $this->newStorage = $this->service->get('BlockChainNewStorage');
        $this->config = $this->service->get('Config');
    }


    public function run() : void
    {
        // получить новые транзации, проверить их и сформировать пул транзаций
        $trx = [];

        // формируем блок
        $lastBlId = $this->storage->getMaxId();
        $lastBl = $lastBlId ? $this->storage->getById($lastBlId) : null;

        $bl = new BlockNew([
            'id' => $lastBlId + 1,
            'prev_block_hash' => $lastBl ? $lastBl->hash : BlockExists::EmptyPrevBlockHash ,
            'trx' => $trx,
            'difficulty' => $this->config->difficulty,
            'is_mining' => $this->config->is_mining,
            'mining_private_key' => $this->config->node_private_key,
            'mining_award' => $this->config->mining_award,
        ]);

        $bl->findProof();

        // сохраняем его
        $this->storage->store($bl);
        $this->newStorage->store($bl); // для рассылки
    }
}