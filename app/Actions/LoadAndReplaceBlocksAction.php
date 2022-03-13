<?php

declare(strict_types=1);

namespace App\Actions;


use App\Classes\NodeDataTransfer;
use App\Classes\ServiceLocator;
use Domain\BlockExists;
use Domain\Interfaces\NodeStorageInterface;
use Domain\Interfaces\TrxStorageInterface;
use Domain\Storages\BlockChainStorageUnion;

class LoadAndReplaceBlocksAction
{

    private ServiceLocator $sl;
    private NodeStorageInterface $ns;

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
        $this->bs = $this->sl->get('BlockChainStorage');
    }

    public function run()
    {
        foreach ($this->ns->getList(false) as $node) {
            $ndt = new NodeDataTransfer($node);

            try {
                $blkJsonArr = $ndt->send([
                    'controller' => 'BlockChain',
                    'method' => 'getLastArr',
                    'params' => [
                        'num' => 10,
                    ]
                ]);
            } catch (\Exception $e) {
                continue;
            }

            $blkArr = [];
            foreach ($blkJsonArr['blkArr'] as $arr) {
                $blkArr[] = BlockExists::fromArr($arr);
            }

            $bcsu = new BlockChainStorageUnion($this->bs, $blkArr);
            $bcsu->merge();
        }

        return ['ok' => 1];
    }
}