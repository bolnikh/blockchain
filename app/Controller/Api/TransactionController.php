<?php


declare(strict_types=1);

namespace App\Controller\Api;



use App\Classes\ServiceLocator;
use Domain\Interfaces\BlockChainStorageInterface;
use Domain\Interfaces\TransactionStorageInterface;
use Domain\TransactionExists;


class TransactionController
{
    private TransactionStorageInterface $trxStorage;
    private BlockChainStorageInterface $blockChainStorage;

    public function __construct()
    {
        $this->trxStorage = ServiceLocator::instance()->get('TrxStorage');
        $this->blockChainStorage = ServiceLocator::instance()->get('BlockChainStorage');
    }


    public function action_insert_trx(array $params) : array
    {
        $result = [];

        foreach ($params['trxs'] as $trxArr) {
            $trx = new TransactionExists($trxArr);
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


    public function action_get_transaction_hashs() : array
    {
        return $this->trxStorage->getKeyList();
    }

    public function action_get_trx(array $params) : TransactionExists
    {
        return $this->trxStorage->get($params['trx_hash']);
    }
}