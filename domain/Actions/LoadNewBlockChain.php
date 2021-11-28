<?php


declare(strict_types=1);

namespace Domain\Actions;

use Domain\BlockChain;
use Domain\RunnableInterface;

class LoadNewBlockChain implements RunnableInterface
{

    public function __construct(
        private BlockChain $newBlockChain,
        private BlockChain $currentBlockChain
    )
    {

    }


    public function run() : void
    {
        if ($this->newBlockChain->length() <= $this->currentBlockChain->length())
        {
            return;
        }

        if ($this->newBlockChain->verify() === false)
        {
            return;
        }

        // replace
    }
}