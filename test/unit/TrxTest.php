<?php

namespace Tests\Unit;


require_once __DIR__ . '\..\..\vendor\autoload.php';



use PHPUnit\Framework\TestCase;
use Domain\KeyMaster;
use Domain\TrxNew;
use Domain\TrxString;
use Domain\Factory\TrxNewFactory;
use Domain\Factory\TrxMiningFactory;




class TrxTest extends TestCase
{

    private function createTrxExists()
    {
        $km = new KeyMaster();
        $km->generateKey();

        $tr_arr = [
            'to' => '12345668890',
            'amount' => 100,
            'ttl' => 3600,
            'private_key' => $km->getPrivateKey(),
        ];

        $tn = new TrxNew($tr_arr);

        $tn_string = $tn->toString();

        $te = (new TrxString($tn_string))->fromString();

        return $te;
    }


    public function testTrCreateVerify()
    {
        $te = $this->createTrxExists();

        $this->assertTrue($te->verifySign());
        $this->assertTrue($te->verifyTtl());
        $this->assertTrue($te->verifyHash());
    }

    public function test_verify_ttl()
    {
        $te = $this->createTrxExists();

        $this->assertTrue($te->verifyTtl());
        $te->ttl = -10;
        $this->assertFalse($te->verifyTtl());
    }

    public function test_verify_sign()
    {
        $km = new KeyMaster();
        $km->generateKey();
        $fake_public_key = $km->getPublicKey();


        $te = $this->createTrxExists();

        $this->assertTrue($te->verifySign());
        $ttl_old = $te->ttl;
        $te->ttl = 500;
        $this->assertFalse($te->verifySign());
        $te->ttl = $ttl_old;

        $this->assertTrue($te->verifySign());
        $from_old = $te->from;
        $te->from = $fake_public_key;
        $this->assertFalse($te->verifySign());
        $te->from = $from_old;


        $this->assertTrue($te->verifySign());
        $to_old = $te->to;
        $te->to = $fake_public_key;
        $this->assertFalse($te->verifySign());
        $te->to = $to_old;



        $this->assertTrue($te->verifySign());
        $amount_old = $te->amount;
        $te->amount = $amount_old + 500;
        $this->assertFalse($te->verifySign());
        $te->amount = $amount_old;
        $this->assertTrue($te->verifySign());
    }


    public function test_timeToSingBlock()
    {
        $te = $this->createTrxExists();

        $this->assertEquals($te->ttl, $te->timeToSingBlock());
    }

    public function test_factory_trx_new()
    {
        $tnf = new TrxNewFactory();

        $tn = $tnf->produce();

        $te = (new TrxString($tn->toString()))->fromString();

        $this->assertTrue($te->verifyHash());
        $this->assertTrue($te->verifySign());
        $this->assertTrue($te->verifyTtl());

    }

    public function test_factory_trx_mining()
    {
        $tf = new TrxMiningFactory();

        $t = $tf->produce();

        $te = (new TrxString($t->toString()))->fromString();

        $this->assertTrue($te->verifyHash());
        $this->assertTrue($te->verifySign());
        $this->assertTrue($te->verifyTtl());
    }

}