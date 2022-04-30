<?php


declare(strict_types=1);

namespace Domain\Interfaces;

use Domain\BlockNew;
use Domain\BlockExists;
use Iterator;

interface BlockChainStorageInterface extends Iterator, BlockChainBalanceInterface
{
    public function store(BlockNew|BlockExists $block) : void;


    public function getById(int $id) : BlockNew|BlockExists|null;



    public function getMaxId() : int;


    /**
     * @param int $num
     * @return BlockExists[]
     */
    public function getLastArr(int $num = 0) : array;

    /**
     * @param int $offset
     * @param int $limit
     * @return BlockExists[]
     */
    public function getAll(int $offset = 0, int $limit = 30) : array;




    public function balancePrevBlock(string $from, int $block_id): int;

    public function isTrxUsed(string $hash, int $before_block_id = 0) : bool;

    public function delete(int $block_id) : void;

    public function getHashes(int $offset = 0, int $limit = 30) : array;
}