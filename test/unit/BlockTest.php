<?php

namespace Tests\Unit;

require_once __DIR__ . '\..\..\vendor\autoload.php';

use Domain\BlockExists;
use Domain\Factory\BlockNewFactory;
use Domain\Factory\BlockMiningFactory;
use PHPUnit\Framework\TestCase;



class BlockTest  extends TestCase
{
    public function test_create_new_block()
    {

        $bnf = new BlockNewFactory();

        $bn = $bnf->produce();

        $this->assertTrue($bn->verifyTrx());
        $this->assertTrue($bn->verifyHash());
        $this->assertTrue($bn->verifyProof());
    }

    public function test_create_new_mining_block()
    {

        $bmf = new BlockMiningFactory();

        $bn = $bmf->produce();

        $this->assertTrue($bn->verifyTrx());
        $this->assertTrue($bn->verifyHash());
        $this->assertTrue($bn->verifyProof());
    }

    public function test_block_has_trx()
    {

        $bnf = new BlockNewFactory();

        $bn = $bnf->produce();

        foreach ($bn->trx as $tr)
        {
            $this->assertTrue($bn->hasTrx($tr->hash));
            $this->assertFalse($bn->hasTrx($tr->hash.'1'));
        }
    }

    public function test_to_from_json_block()
    {

        $bnf = new BlockNewFactory();

        $bn = $bnf->produce();

        $json_data = $bn->toJson();
        $be = BlockExists::fromJson($json_data);

        $this->assertEquals($bn->id, $be->id);
        $this->assertEquals($bn->hash, $be->hash);
        $this->assertEquals($bn->prev_block_hash, $be->prev_block_hash);
        $this->assertEquals($bn->created_at, $be->created_at);
        $this->assertEquals($bn->trx_hash, $be->trx_hash);
        $this->assertEquals($bn->difficulty, $be->difficulty);
        $this->assertEquals($bn->proof, $be->proof);

        for ($i = 0; $i < sizeof($bn->trx); $i++) {
            $trx_n = $bn->trx[$i];
            $trx_e = $be->trx[$i];

            $this->assertEquals($trx_n->from, $trx_e->from);
            $this->assertEquals($trx_n->to, $trx_e->to);
            $this->assertEquals($trx_n->amount, $trx_e->amount);
            $this->assertEquals($trx_n->hash, $trx_e->hash);
        }

    }
}