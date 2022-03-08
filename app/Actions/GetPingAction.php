<?php

declare(strict_types=1);

namespace App\Actions;

use App\Classes\NodeDataTransfer;

class GetPingAction
{


    public function run(Node $node)
    {
        $ndt = new NodeDataTransfer($node);
        $ndt->ping();
    }
}