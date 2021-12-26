<?php


declare(strict_types=1);

namespace Domain\Interfaces;

use Domain\BlockExists;
use Domain\BlockNew;
use Domain\TransactionNew;


interface BlockChainBalanceValidateInterface
{

    public function validateBlockTransactionsBalance(BlockChainStorageInterface $blockChainStorage, BlockExists|int $bl) : bool;

    public function validateNewTransactionBalance(BlockChainStorageInterface $blockChainStorage, BlockNew $blockNew, TransactionNew $transactionNew) : bool;

    public function validateNewBlock(BlockChainStorageInterface $blockChainStorage, BlockNew $blockNew) : bool;
}