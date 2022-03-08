<?php

declare(strict_types=1);

namespace App\Actions;

use App\Classes\ServiceLocator;
use Domain\Interfaces\TrxStorageInterface;


class DeleteExpiredTrxAction
{
    private ServiceLocator $sl;
    private TrxStorageInterface $ts;

    public function __construct()
    {
        $this->sl = ServiceLocator::instance();

        /**
         * @var TrxStorageInterface
         */
        $this->ts = $this->sl->get('TrxStorage');
    }

    public function run()
    {
        $this->ts->deleteAllByTtl();
    }
}