<?php


declare(strict_types=1);

namespace Domain\Storages;


use Domain\BlockChainBalanceValidate;
use Domain\BlockExists;
use Domain\BlockNew;
use Domain\Interfaces\BlockChainStorageInterface;


/**
 * Class BlockChainStorageUnion
 *
 * Задача слияния текущего блокчейна с новым, более длинным
 * Предполагается, что есть совпадение блоков, то есть уже есть история
 * если совпадения блоков нет, то надо просто перезаписать текущий блокчейн
 *
 * @package Domain\Storages
 */
class BlockChainStorageUnion  implements BlockChainStorageInterface
{
    private int $position = 0;

    // последний совпадающий ид в $bs и $newBlocks
    private int $lastBsId;

    const NO_NEED_MERGE = 1;
    const ERROR_VALIDATE_NEW_BLOCKS = 2;
    const MERGED = 3;

    public function __construct(
        private BlockChainStorageInterface $bs,
        private array $newBlocks
    ) {

    }

    public function init()
    {
        assert(sizeof($this->newBlocks) > 0);
        $this->findLastBsId();
    }

    public function merge() : int
    {
        $this->init();
        if ($this->needMergeNewBlocks())
        {
            if ($this->validateNewBlocks())
            {
                $this->storeNewBlocks();
                return self::MERGED;
            } else {
                return self::ERROR_VALIDATE_NEW_BLOCKS;
            }
        } else {
            return self::NO_NEED_MERGE;
        }
    }

    /**
     * Последний актуальный блок ид в $bs
     */
    public function findLastBsId() : void
    {
        foreach ($this->newBlocks as $nb)
        {
            $ob = $this->bs->getById($nb->id);
            if ($ob && $ob->hash == $nb->hash)
            {
                $this->lastBsId = $nb->id;
            } else {
                break;
            }
        }

        if (is_null($this->lastBsId))
        {
            $this->lastBsId = 0; // полностью берем блоки из массива $newBlocks
        }
    }

    /**
     * Надо смержить новые блоки, если с ними длина блокчейна будет больше
     *
     * @return bool
     */
    public function needMergeNewBlocks() : bool
    {
        if (is_null($this->lastBsId))
        {
            $this->findLastBsId();
        }

        $countNewBlocks = 0;
        foreach ($this->newBlocks as $bl) {
            if ($bl->id > $this->lastBsId) {
                $countNewBlocks++;
            }
        }

        return $this->bs->getMaxId() < $this->lastBsId + $countNewBlocks;
    }

    /**
     *
     */
    public function validateNewBlocks() : bool
    {
        $bcbv = new BlockChainBalanceValidate();
        foreach ($this->newBlocks as $bl) {
            if ($bl->id > $this->lastBsId) {
                if (!$bl->verifyBlock()) {
                    return false;
                }
                if (!$bcbv->validateBlockTransactionsBalance($this, $bl))
                {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * store newBlock to storage
     */
    public function storeNewBlocks()
    {
        foreach ($this->newBlocks as $bl)
        {
            if ($bl->id > $this->lastBsId)
            {
                $this->store($bl);
            }
        }
    }

    public function store(BlockNew|BlockExists $bl) : void
    {
        $this->bs->store($bl);
    }

    public function getById(int $id) : BlockNew|BlockExists|null
    {
        assert($id >= 0);

        if ($id <= $this->lastBsId)
        {
            return $this->bs->getById($id);
        } else {
            foreach ($this->newBlocks as $bl)
            {
                if ($bl->id == $id)
                {
                    return $bl;
                }
            }
        }

        return null;
    }



    public function getLast()  : BlockNew|BlockExists|null
    {
        return end($this->newBlocks);
    }

    public function getMaxId() : int
    {
        $last = $this->getLast();
        if ($last) {
            return $last->id;
        }
        return 0;
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
            $max_id = $this->getMaxId();
            $arr = [];
            for ($i = $max_id - $num; $i <= $max_id; $i++)
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

        $max_id = $this->getMaxId();
        $arr = [];
        for ($i = $offset; $i <= min($offset + $limit, $max_id); $i++)
        {
            $arr[] = $this->getById($i);
        }
        return $arr;
    }

    public function balance(string $from, int $block_id = 0): int
    {
        assert($block_id >= 0);

        $balance = 0;
        $max_id = $block_id ?: $this->getMaxId();

        for ($i = 1; $i <= $max_id; $i++)
        {
            $bl = $this->getById($i);

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
        return $this->getById($this->position + 1);
    }

    public function key() : mixed {
        return $this->position;
    }

    public function next() : void  {
        ++$this->position;
    }

    public function valid() : bool {
        return $this->position + 1 <= $this->getMaxId();
    }
}