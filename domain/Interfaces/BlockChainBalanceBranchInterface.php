<?php

declare(strict_types=1);

namespace Domain\Interfaces;


interface BlockChainBalanceBranchInterface {
    const DefBranch = 0;

    public function balance(string $from, int $block_id = 0, int $branch = self::DefBranch) : int;
}