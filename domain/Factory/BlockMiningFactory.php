<?php


declare(strict_types=1);

namespace Domain\Factory;

use \Domain\Factory\BlockNewFactory;


class BlockMiningFactory extends BlockNewFactory
{
    private function prepare() : void
    {
        if (empty($this->mining_private_key))
        {
            $key = self::prepareKey();
            $this->mining_private_key = $key['private_key'];
        }
    }

}