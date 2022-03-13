<?php

declare(strict_types=1);

namespace App\Actions;

use App\Classes\NodeDataTransfer;
use App\Classes\ServiceLocator;
use Domain\Interfaces\NodeStorageInterface;
use Domain\Interfaces\TrxStorageInterface;
use Domain\TrxExists;

class LoadTrxAction
{
    private ServiceLocator $sl;
    private NodeStorageInterface $ns;
    private TrxStorageInterface $ts;

    public function __construct()
    {
        $this->sl = ServiceLocator::instance();

        /**
         * @var NodeStorageInterface
         */
        $this->ns = $this->sl->get('NodeStorage');

        /**
         * @var TrxStorageInterface
         */
        $this->ts = $this->sl->get('TrxStorage');
    }

    public function run()
    {
        foreach ($this->ns->getList(false) as $node) {
            $ndt = new NodeDataTransfer($node);

            try {
                $keys = $ndt->send([
                    'controller' => 'Trx',
                    'method' => 'getAllTrxHashes',
                    'params' => [
                    ]
                ]);
            } catch (\Exception $e) {
                continue;
            }

            foreach ($keys as $key) {
                if (!$this->ts->isExists($key)) {
                    try {
                        $trx = $ndt->send([
                            'controller' => 'Trx',
                            'method' => 'getTrx',
                            'params' => [
                                'trx_hash' => $key,
                            ]
                        ]);
                    } catch (\Exception $e) {
                        continue;
                    }

                    $t = new TrxExists($trx['trx']);
                    $this->ts->store($t);
                }
            }
        }
    }
}