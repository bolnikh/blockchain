<?php

declare(strict_types=1);

namespace App\Actions;

use App\Classes\NodeDataTransfer;
use App\Classes\ServiceLocator;
use Domain\Interfaces\NodeStorageInterface;
use Domain\Interfaces\TrxStorageInterface;

class SendAllTrxAction
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

        /**
         * @var NodeStorageInterface
         */
        $this->ns = $this->sl->get('NodeStorage');


    }

    public function run()
    {
        $trxArr = [];
        foreach ($this->ts->getKeyList() as $key) {
            $trxArr[] = json_encode($this->ts->get($key));
        }

        if (empty($trxArr)) {
            return;
        }

        foreach ($this->ns->getList() as $node) {
            $ndt = new NodeDataTransfer($node);

            try {
                $ndt->send([
                    'controller' => 'Trx',
                    'method' => 'insertTrx',
                    'params' => [
                        'trxs' => $trxArr,
                    ]
                ]);
            } catch (\Exception $e) {

            }
        }
    }
}