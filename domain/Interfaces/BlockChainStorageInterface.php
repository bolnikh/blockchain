<?php


declare(strict_types=1);

namespace Domain\Interfaces;

use Domain\BlockNew;
use Domain\BlockExists;

interface BlockChainStorageInterface
{
    public function store(BlockNew|BlockExists $block) : bool;


    public function getById(int $id) : BlockNew|BlockExists|null;


    public function getByHash(string $hash) : BlockNew|BlockExists|null;

    public function getMaxId() : int;


    /**
     * @param int $num
     * @return Block[]
     */
    public function getLastArr(int $num = 0) : array;

    /**
     * @param int $offset
     * @param int $limit
     * @return Block[]
     */
    public function getAll(int $offset = 0, int $limit = 30) : array;






}