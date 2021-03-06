<?php

declare(strict_types=1);

namespace Domain\Actions;


use Domain\Factory\TrxNewBalancedFactory;
use Domain\Interfaces\RunnableInterface;


class CreateNewTestTrx implements RunnableInterface
{

    public function __construct()
    {
    }

    public function run() : void
    {
        $factory = new TrxNewBalancedFactory();
        $trx = $factory->get();

        if ($trx) {
            $getn = new RequestExternalNewTrx($trx);
            $getn->run();
        }
    }
}