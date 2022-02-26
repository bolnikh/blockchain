<?php


declare(strict_types=1);

namespace Domain\Storages;


use Domain\BlockExists;
use Domain\BlockNew;


class BlockChainStorageMemory extends BlockChainStorageAbstract
{
    protected $position = 1;
    private array $blockChain = [];

    public function __construct() {
        parent::__construct();
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
        assert($id > 0);
        if (isset($this->blockChain[$id-1])) {
            return $this->blockChain[$id-1];
        }
        return null;
    }


    public function getMaxId() : int
    {
        return sizeof($this->blockChain);
    }

    public function delete(int $block_id) : void
    {
        assert($block_id > 0);
        if (isset($this->blockChain[$block_id-1])) {
            unset($this->blockChain[$block_id-1]);
        }
    }

}
