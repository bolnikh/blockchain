<?php


declare(strict_types=1);

namespace App\Controller\Api;


use App\Classes\ServiceLocator;
use Domain\BlockExists;
use Domain\Interfaces\BlockChainStorageInterface;
use Domain\Storages\BlockChainStorageUnion;


class BlockChainController
{
    private BlockChainStorageInterface $blockChainStorage;

    public function __construct()
    {
          $this->blockChainStorage = ServiceLocator::instance()->get('BlockChainStorage');
    }


    public function action_getMaxId()
    {
        return ['max_id' => $this->blockChainStorage->getMaxId()];
    }

    public function action_getById(array $params)
    {
        return ['block' => $this->blockChainStorage->getById($params['block_id'])];
    }

    public function action_getBalance(array $params)
    {
        return ['balance' => $this->blockChainStorage->balance($params['from'])];
    }

    public function action_addBlocks(array $params)
    {
        $newBlocks = [];
        foreach ($params['newBlocks'] as $nb_json) {
            $newBlocks[] = BlockExists::fromJson($nb_json);
        }

        $bcsu = new BlockChainStorageUnion($this->blockChainStorage, $newBlocks);

        return ['result' => $bcsu->merge()];
    }
}