<?php


declare(strict_types=1);

namespace Domain;

interface BlockChainBalanceInterface
{
    /**
     * @param string $from
     * @param int $block_id | 0 ==  last
     * @return int
     */
    public function balance(string $from, int $block_id = 0) : int;
}