<?php


declare(strict_types=1);

namespace Domain\Factory;



class BlockMiningFactory extends BlockNewFactory
{
    protected function prepare() : void
    {
        if (empty($this->mining_private_key))
        {
            $key = self::prepareKey();
            $this->mining_private_key = $key['private_key'];
        }
    }

}