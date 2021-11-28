<?php

namespace Tests\Unit;

require_once __DIR__ . '\..\..\vendor\autoload.php';

use PHPUnit\Framework\TestCase;
use Domain\KeyMaster;
use Domain\Sign;


class BlockTest  extends TestCase
{
    public function create_new_block_test()
    {
        $bn = new BlockNew([
            'id' => 1,
            'prev_block_hash' => 0,
            'created_at' => time(),
            'transactions' => [],
            'difficulty' => '0000f',
            'private_key',
            'is_mining' => true,
            'mining_award' => 100
        ]);
    }


}