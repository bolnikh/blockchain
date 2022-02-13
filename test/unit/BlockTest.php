<?php

namespace Tests\Unit;

require_once __DIR__ . '\..\..\vendor\autoload.php';

use Domain\Factory\BlockNewFactory;
use Domain\Factory\BlockMiningFactory;
use PHPUnit\Framework\TestCase;



class BlockTest  extends TestCase
{
    public function test_create_new_block()
    {

        $bnf = new BlockNewFactory();

        $bn = $bnf->produce();

        $this->assertTrue($bn->verifyTransactions());
        $this->assertTrue($bn->verifyHash());
        $this->assertTrue($bn->verifyProof());
    }

    public function test_create_new_mining_block()
    {

        $bmf = new BlockMiningFactory();

        $bn = $bmf->produce();

        $this->assertTrue($bn->verifyTransactions());
        $this->assertTrue($bn->verifyHash());
        $this->assertTrue($bn->verifyProof());
    }

    public function test_block_has_trx()
    {

        $bnf = new BlockNewFactory();

        $bn = $bnf->produce();

        foreach ($bn->transactions as $tr)
        {
            $this->assertTrue($bn->hasTrx($tr->hash));
            $this->assertFalse($bn->hasTrx($tr->hash.'1'));
        }
    }
}