<?php


declare(strict_types=1);

namespace Domain\Interfaces;

use Domain\BlockExists;
use Domain\BlockNew;
use Domain\TrxNew;


interface BlockChainBalanceValidateInterface
{

    public function validateBlockTrxBalance(BlockChainStorageInterface $blockChainStorage, BlockExists|int $bl) : bool;

    public function validateNewTrxBalance(BlockChainStorageInterface $blockChainStorage, BlockNew $blockNew, TrxNew $transactionNew) : bool;

    public function validateNewTrxBalanceAgainstTrxs(BlockChainStorageInterface $blockChainStorage, array $trxsSelected, TrxNew $transactionNew): bool;

    public function validateNewBlock(BlockChainStorageInterface $blockChainStorage, BlockNew $blockNew) : bool;
}