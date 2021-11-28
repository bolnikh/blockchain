<?php


declare(strict_types=1);

namespace Domain;

interface BlockChainStorageInterface
{
    public function store(Block $block) : bool;


    public function getById(int $id) : ?Block;


    public function getByHash(string $hash) : ?Block;

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