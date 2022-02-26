<?php

declare(strict_types=1);

namespace Domain\Actions;


use Domain\Factory\TransactionNewBalancedFactory;
use Domain\Interfaces\RunnableInterface;


class CreateNewTestTrx implements RunnableInterface
{

    public function __construct()
    {
    }

    public function run() : void
    {
        $factory = new TransactionNewBalancedFactory();
        $trx = $factory->get();

        if ($trx) {
            $getn = new GetExternalNewTrx($trx);
            $getn->run();
        }
    }
}