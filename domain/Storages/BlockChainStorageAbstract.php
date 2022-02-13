<?php


declare(strict_types=1);

namespace Domain\Storages;


use Domain\BlockExists;
use Domain\BlockNew;
use Domain\Interfaces\BlockChainStorageInterface;


abstract class BlockChainStorageAbstract implements BlockChainStorageInterface
{
    protected $position = 1;

    public function __construct() {
        $this->rewind();

    }

    public function getFirst()  : BlockNew|BlockExists|null
    {
        return $this->getById(1);
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
        return $this->getById($this->getMaxId());
    }

    /**
     * @param int $num
     * @return BlockExists[]
     */
    public function getLastArr(int $num = 0) : array
    {
        if ($num == 0) {
            return [$this->getLast()];
        } else {
            $arr = [];
            $max_id = $this->getMaxId();
            if ($max_id == 0)
            {
                return[];
            }
            if ($max_id < $num)
            {
                $start = 1;
                $end = $max_id;
            } else {
                $start = $max_id - $num;
                $end = $max_id;
            }
            for ($i = $start; $i < $end; $i++)
            {
                $arr[] = $this->getById($i);
            }

            return $arr;
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

        if ($offset == 0)
        {
            $offset = 1;
        }

        $max_id = $this->getMaxId();
        if ($offset > $max_id)
        {
            return [];
        }
        $end = min($max_id, $offset + $limit);

        $arr = [];
        for ($i = $offset; $i <= $end; $i++)
        {
            $arr[] = $this->getById($i);
        }

        return $arr;
    }


    public function balance(string $from, int $block_id = 0): int
    {
        assert($block_id >= 0);

        $balance = 0;

        foreach ($this as $bl)
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


    public function isTrxUsed(string $hash, int $before_block_id = 0) : bool
    {
        foreach ($this as $bl) {
            if ($before_block_id > 0 && $before_block_id <= $bl->id) { // check all blocks before
                return false;
            }
            foreach ($bl->transactions as $tr) {
                if ($tr->hash === $hash) {
                    return true;
                }
            }
        }

        return false;
    }



    public function rewind() : void {
        $this->position = 1;
    }

    public function current() : mixed {
        return $this->getById($this->position);
    }

    public function key() : mixed {
        return $this->position;
    }

    public function next() : void {
        ++$this->position;
    }

    public function valid() : bool {
        return $this->position <= $this->getMaxId();
    }

}
