<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Interfaces\BlockChainStorageInterface;

/**
 * Class BlockChainUnion
 *
 * union current blockchain with another blockchain
 *
 * @package Domain\Actions
 */
class BlockChainUnion
{
    public function __construct(
        private BlockChainStorageInterface $bs,
        private array $newBlocks)
    {

    }

    /**
     * Check if newBlock longer and valid
     *
     */
    public function validate()
    {

    }
}