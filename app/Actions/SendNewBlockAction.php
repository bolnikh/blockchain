<?php

declare(strict_types=1);

namespace App\Actions;


use App\Classes\Config;
use App\Classes\NodeDataTransfer;
use App\Classes\ServiceLocator;
use Domain\Interfaces\BlockChainStorageInterface;
use Domain\Interfaces\NodeStorageInterface;


class SendNewBlockAction
{
    private ServiceLocator $sl;
    private BlockChainStorageInterface $bs;
    private NodeStorageInterface $ns;

    public function __construct()
    {
        $this->sl = ServiceLocator::instance();

        /**
         * @var BlockChainStorageInterface
         */
        $this->bs = $this->sl->get('BlockChainStorage');

        /**
         * @var NodeStorageInterface
         */
        $this->ns = $this->sl->get('NodeStorage');
    }


    public function run() {

        $blkArr = [];
        foreach ($this->bs->getLastArr(100) as $bl) {
            $blkArr[] = json_encode($bl);
        }

        if (empty($blkArr)) {
            return;
        }

        foreach ($this->ns->getList() as $node) {
            $ndt = new NodeDataTransfer($node);

            try {
                $ndt->send([
                    'controller' => 'BlockChain',
                    'method' => 'addBlocks',
                    'params' => [
                        'newBlocks' => $blkArr,
                    ]
                ]);
            } catch (\Exception $e) {

            }
        }
    }

}