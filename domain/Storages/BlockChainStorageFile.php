<?php


declare(strict_types=1);

namespace Domain\Storages;


use App\Interfaces\ServiceInterface;
use Domain\BlockExists;
use Domain\BlockNew;
use Domain\Interfaces\BlockChainStorageInterface;
use Domain\TransactionExists;


class BlockChainStorageFile implements BlockChainStorageInterface, ServiceInterface
{
    private int $position = 0;


    private $storageDir = __DIR__.'/../../storage/files/';

    public function __construct() {
        $this->position = 0;

    }


    public function store(BlockNew|BlockExists $block) : void
    {
        $json_obj = json_encode($block);
        file_put_contents($this->blockFileName($block), $json_obj);

    }

    private function blockFileName(BlockNew|BlockExists|int $block) : string
    {
        if (is_int($block))
        {
            return $this->storageDir.'blocks/'.$block.'.json';
        }
        else
        {
            return $this->storageDir.'blocks/'.$block->id.'.json';
        }
    }

    public function getById(int $id) : BlockNew|BlockExists|null
    {
        assert($id >= 0);

        $blockFileName = $this->blockFileName($id);

        if (file_exists($blockFileName))
        {
            $json_data = file_get_contents($blockFileName);
            $bn_arr = json_decode($json_data, true, 10, JSON_THROW_ON_ERROR);

            $trans = [];
            foreach ($bn_arr['transactions'] as $tr)
            {
                $trans[] = new TransactionExists($tr);
            }

            $bn_arr['transactions'] = $trans;

            return new BlockExists($bn_arr);
        }
        return null;
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

    public function getMaxId() : int
    {
        $files = scandir($this->storageDir.'blocks/');
        $max_id = 0;

        foreach ($files as $file)
        {
            $file_id = intval($file);
            if ($file_id > $max_id)
            {
                $max_id = $file_id;
            }
        }
        return $max_id;
    }

//    public function sizeof() {
//        return sizeof($this->blockChain);
//    }

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



    public function rewind() {
        $this->position = 1;
    }

    public function current() {
        return $this->getById($this->position);
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return $this->position <= $this->getMaxId();
    }

}