<?php


declare(strict_types=1);

namespace App\Controller\Api;



use App\Classes\ServiceLocator;
use Domain\Interfaces\BlockChainStorageInterface;
use Domain\Interfaces\TrxStorageInterface;
use Domain\TrxExists;
use Domain\TrxString;


class TrxController
{
    private TrxStorageInterface $trxStorage;
    private BlockChainStorageInterface $blockChainStorage;

    public function __construct()
    {
        $this->trxStorage = ServiceLocator::instance()->get('TrxStorage');
        $this->newTrxStorage = ServiceLocator::instance()->get('NewTrxStorage');
        $this->blockChainStorage = ServiceLocator::instance()->get('BlockChainStorage');
    }


    public function action_insertTrx(array $params) : array
    {
        $result = [];

        foreach ($params['trxs'] as $trxStr) {
            $trx = (new TrxString($trxStr))->fromString();
            if ($this->trxStorage->isExists($trx->hash)) {
                $result[$trx->hash] = ['error' => 'already stored'];
                continue;
            }
            if (!$trx->isValidForExistsBlock()) {
                $result[$trx->hash] = ['error' => 'not_valid'];
                continue;
            }
            if ($this->blockChainStorage->balance($trx->from) < $trx->amount) {
                $result[$trx->hash] = ['error' => 'insufficient_balance'];
                continue;
            }

            $this->trxStorage->store($trx);
            $result[$trx->hash] = ['success' => 'stored'];
        }

        return $result;
    }


    public function action_getAllTrxHashes() : array
    {
        return $this->trxStorage->getKeyList();
    }

    public function action_getTrx(array $params) : TrxExists
    {
        return $this->trxStorage->get($params['trx_hash']);
    }

}