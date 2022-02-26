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
class BlockChainStorageUnion  extends BlockChainStorageAbstract
{


    // последний совпадающий ид в $bs и $newBlocks
    private int $lastBsId;

    const NO_NEED_MERGE = 1;
    const ERROR_VALIDATE_NEW_BLOCKS = 2;
    const MERGED = 3;

    public function __construct(
        private BlockChainStorageInterface $bs,
        private array $newBlocks
    ) {
        parent::__construct();
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
                if (!$bcbv->validateBlockTrxBalance($this, $bl))
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



    public function delete(int $block_id) : void
    {
        // not used
    }


}