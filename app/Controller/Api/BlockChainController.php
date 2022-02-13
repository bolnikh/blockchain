<?php


declare(strict_types=1);

namespace App\Controller\Api;


use App\Classes\ServiceLocator;
use Domain\Interfaces\BlockChainStorageInterface;


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
}