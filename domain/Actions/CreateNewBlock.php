<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Interfaces\RunnableInterface;

/**
 * Class CreateNewBlock
 *
 * create new block for blockchain
 * collect transactions
 * store them into block
 * produce block
 * store it to blockchain
 *
 * @package Domain\Actions
 */
class CreateNewBlock implements RunnableInterface
{

    public function __construct()
    {

    }


    public function run() : void
    {

    }
}