<?php

namespace Tests\Unit;

require_once __DIR__ . '\..\..\vendor\autoload.php';

use Domain\BlockExists;
use Domain\Factory\BlockNewFactory;
use Domain\TrxExists;
use PHPUnit\Framework\TestCase;


class JsonTest extends TestCase
{

    public function test_obj()
    {
        $bnf = new BlockNewFactory();
        $bn = $bnf->produce();

        $json_data = json_encode($bn);

        $bn_arr = json_decode($json_data, true);

        $trans = [];
        foreach ($bn_arr['trx'] as $tr)
        {
            $trans[] = new TrxExists($tr);
        }

        $bn_arr['trx'] = $trans;

        $bl_exists = new BlockExists($bn_arr);

        $this->assertTrue($bl_exists->verifyTrx());
        $this->assertTrue($bl_exists->verifyHash());
        $this->assertTrue($bl_exists->verifyProof());

        $this->assertEquals($bn->id, $bl_exists->id);
        $this->assertEquals($bn->hash, $bl_exists->hash);
        $this->assertEquals($bn->prev_block_hash, $bl_exists->prev_block_hash);
        $this->assertEquals($bn->created_at, $bl_exists->created_at);
        $this->assertEquals($bn->trx_hash, $bl_exists->trx_hash);
        $this->assertEquals($bn->difficulty, $bl_exists->difficulty);
        $this->assertEquals($bn->proof, $bl_exists->proof);
    }

}