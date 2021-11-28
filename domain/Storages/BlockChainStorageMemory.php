<?php


declare(strict_types=1);

namespace Domain;


class BlockChainStorageMemory implements BlockChainStorageInterface
{
    private array $blockChain = [];

    public function setBlockChain(BlockChain $blockChain)
    {
        $this->blockChain = $blockChain;
    }

    public function store(Block $block) : bool
    {
        $this->blockChain[] = $block;
    }


    public function getById(int $id) : ?Block
    {
        foreach ($this->blockChain as $bl)
        {
            if ($bl->id == $id)
            {
                return $bl;
            }
        }
        return null;
    }


    public function getByHash(string $hash) : ?Block
    {
        foreach ($this->blockChain as $bl)
        {
            if ($bl->hash == $hash)
            {
                return $bl;
            }
        }
        return null;
    }

    public function getFirst()  : ?Block
    {

    }

    public function getNext()  : ?Block
    {

    }

    public function getPrev()  : ?Block
    {

    }

    public function getLast()  : ?Block
    {

    }


    /**
     * @param int $num
     * @return Block[]
     */
    public function getLastArr(int $num = 0) : array
    {
        if ($num == 0) {
            return end($this->blockChain);
        } else {
            return array_slice($this->blockChain, -$num, $num);
        }
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return Block[]
     */
    public function getAll(int $offset = 0, int $limit = 30) : array
    {
        return array_slice($this->blockChain, $offset, $limit);
    }

    public function balance(string $from): int
    {
        $balance = 0;

        foreach ($this->blockChain as $bl)
        {
            foreach ($bl->transactions as $tr)
            {
                if ($tr->form == $from)
                {
                    $balance -= $tr->amount;
                }
                if ($tr->to == $from)
                {
                    $balance += $tr->amount;
                }
            }
        }

        return $balance;
    }

    public function validateBlockChain(BlockChain $blockChain) : bool
    {

    }


//    public function validateNewTransactions(BlockChain $blockChain, array $transactions) : array
//    {}

    public function validateNewTransaction(BlockChain $blockChain, TransactionNew $transactionNew) : bool
    {}

    public function validateNewBlock(BlockChain $blockChain, BlockNew $blockNew) : bool
    {}

}