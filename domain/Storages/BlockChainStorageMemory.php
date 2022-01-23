<?php


declare(strict_types=1);

namespace Domain\Storages;


use Domain\BlockExists;
use Domain\BlockNew;
use Domain\Interfaces\BlockChainStorageInterface;


class BlockChainStorageMemory implements BlockChainStorageInterface
{
    private $position = 0;
    private array $blockChain = [];

    public function __construct() {
        $this->position = 0;
    }

    public function setBlockChain(array $blockChain)
    {
        $this->blockChain = $blockChain;
    }

    public function store(BlockNew|BlockExists $block) : void
    {
        $this->blockChain[$block->id - 1] = $block;
    }


    public function getById(int $id) : BlockNew|BlockExists|null
    {
        assert($id >= 0);
        if (isset($this->blockChain[$id-1])) {
            return $this->blockChain[$id-1];
        }
        return null;
    }



    public function getFirst()  : BlockNew|BlockExists|null
    {
        if (isset($this->blockChain[0]))
        {
            return $this->blockChain[0];
        }
        return null;
    }

    public function getNext(int $curr_id)  : BlockNew|BlockExists|null
    {
        assert($curr_id >= 0);
        return $this->getById($curr_id + 1);
    }

    public function getPrev(int $curr_id)  : BlockNew|BlockExists|null
    {
        assert($curr_id > 0);
        return $this->getById($curr_id - 1);
    }

    public function getLast()  : BlockNew|BlockExists|null
    {
        if (sizeof($this->blockChain) > 0)
        {
            return $this->blockChain[sizeof($this->blockChain) - 1];
        }
        return null;
    }

    public function getMaxId() : int
    {
        $last = $this->getLast();
        if ($last) {
            return $last->id;
        }
        return 0;
    }

    public function sizeof() {
        return sizeof($this->blockChain);
    }

    /**
     * @param int $num
     * @return BlockExists[]
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
     * @return BlockExists[]
     */
    public function getAll(int $offset = 0, int $limit = 30) : array
    {
        assert($offset >= 0);
        assert($limit > 0);

        return array_slice($this->blockChain, $offset, $limit);
    }

    public function balance(string $from, int $block_id = 0): int
    {
        assert($block_id >= 0);

        $balance = 0;

        foreach ($this->blockChain as $bl)
        {
            if ($block_id > 0 && $bl->id > $block_id)
            {
                break;
            }
            foreach ($bl->transactions as $tr)
            {
                if ($tr->from == $from)
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

    public function balancePrevBlock(string $from, int $block_id): int
    {
        assert($block_id >= 0);

        if ($block_id <= 1)
        {
            return 0;
        }

        return $this->balance($from, $block_id - 1);
    }



    public function rewind() : void {
        $this->position = 0;
    }

    public function current() : mixed {
        return $this->blockChain[$this->position];
    }

    public function key() : mixed {
        return $this->position;
    }

    public function next() : void {
        ++$this->position;
    }

    public function valid() : bool {
        return isset($this->blockChain[$this->position]);
    }


}
