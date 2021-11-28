<?php


declare(strict_types=1);

namespace Domain;

interface BlockChainManageInterface
{

    public function validateBlockChainEnd(BlockChain $blockChain) : bool;


    public function validateNewTransaction(BlockChain $blockChain, TransactionNew $transactionNew) : bool;

    public function validateNewBlock(BlockChain $blockChain, BlockNew $blockNew) : bool;
}