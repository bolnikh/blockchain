<?php


declare(strict_types=1);

namespace App\Controller\Api;


use App\Classes\ServiceLocator;
use Domain\BlockExists;
use Domain\Interfaces\BlockChainStorageInterface;
use Domain\Storages\BlockChainStorageUnion;
use JetBrains\PhpStorm\ArrayShape;


class BlockChainController
{
    private BlockChainStorageInterface $blockChainStorage;

    public function __construct()
    {
          $this->blockChainStorage = ServiceLocator::instance()->get('BlockChainStorage');
    }


    public function action_getMaxId() : array
    {
        return ['max_id' => $this->blockChainStorage->getMaxId()];
    }

    public function action_getById(array $params) : array
    {
        return ['block' => $this->blockChainStorage->getById($params['block_id'])];
    }

    public function action_getBalance(array $params) : array
    {
        return ['balance' => $this->blockChainStorage->balance($params['from'])];
    }

    public function action_addBlocks(array $params) : array
    {
        $newBlocks = [];
        foreach ($params['newBlocks'] as $nb_json) {
            $newBlocks[] = BlockExists::fromJson($nb_json);
        }

        $bcsu = new BlockChainStorageUnion($this->blockChainStorage, $newBlocks);

        return ['result' => $bcsu->merge()];
    }

    public function action_getLastArr(array $params) : array
    {
        $num = intval($params['num'] ?? 10);

        $blkArr = $this->blockChainStorage->getLastArr($num);

        return ['blkArr' => $blkArr];
    }


    public function action_getAll(array $params) : array
    {
        $offset = intval($params['offset'] ?? 0);
        $limit = intval($params['limit'] ?? 10);

        $allArr = $this->blockChainStorage->getAll($offset, $limit);

        return ['allArr' => $allArr];
    }

    public function action_getHashes(array $params) : array
    {
        $offset = intval($params['offset'] ?? 0);
        $limit = intval($params['limit'] ?? 100);

        $hashArr = $this->blockChainStorage->getHashes($offset, $limit);

        return ['hashArr' => $hashArr];
    }
}