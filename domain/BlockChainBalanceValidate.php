<?php

declare(strict_types = 1);

namespace Domain;


use Domain\Interfaces\BlockChainBalanceValidateInterface;
use Domain\Interfaces\BlockChainStorageInterface;


class BlockChainBalanceValidate implements BlockChainBalanceValidateInterface
{

    public function validateBlockTransactionsBalance(BlockChainStorageInterface $blockChainStorage, int|BlockExists|BlockNew $bl): bool
    {
        if (is_int($bl))
        {
            $bl = $blockChainStorage->getById($bl);
        }

        $tr_spend = [];
        foreach ($bl->transactions as $tr)
        {
            if (!isset($tr_spend[$tr->from]))
            {
                $tr_spend[$tr->from] = 0;
            }
            $tr_spend[$tr->from] += $tr->amount;
        }

        foreach ($tr_spend as $from => $spend)
        {
            if ($from == TransactionExists::MINING_FROM)
            {
                continue;
            }
            if ($spend > $blockChainStorage->balancePrevBlock($from, $bl->id))
            {
                return false;
            }
        }

        return true;
    }

    public function validateNewTransactionBalance(BlockChainStorageInterface $blockChainStorage, BlockNew $blockNew, TransactionNew $transactionNew): bool
    {
        if ($transactionNew->from == TransactionExists::MINING_FROM)
        {
            return true;
        }

        $tr_spend = 0;
        foreach ($blockNew->transactions as $tr)
        {
            if ($tr->from == $transactionNew->from)
            {
                $tr_spend += $tr->amount;
            }
        }

        return ($tr_spend + $transactionNew->amount) <= $blockChainStorage->balance($transactionNew->from);
    }

    public function validateNewBlock(BlockChainStorageInterface $blockChainStorage, BlockNew $blockNew): bool
    {
        // TODO: Implement validateNewBlock() method.
        // не тоже самое как validateBlockTransactionsBalance ??

        return true;
    }
}