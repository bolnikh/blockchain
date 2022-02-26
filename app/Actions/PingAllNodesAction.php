<?php

declare(strict_types=1);

namespace App\Actions;

use App\Classes\NodeDataTransfer;
use App\Classes\ServiceLocator;
use Domain\Interfaces\NodeStorageInterface;

class PingAllNodesAction
{

    private ServiceLocator $sl;
    private NodeStorageInterface $ns;

    public function __construct()
    {
        $this->sl = ServiceLocator::instance();
        $this->ns = $this->sl->get('NodeStorage');
    }


    public function run() : void
    {
        foreach ($this->ns->getList() as $node) {
            $ndt = new NodeDataTransfer($node);
            $ndt->ping();
        }
    }
}