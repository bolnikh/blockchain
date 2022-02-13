<?php

namespace Tests\Unit;


require_once __DIR__ . '\..\..\vendor\autoload.php';



use PHPUnit\Framework\TestCase;
use Domain\KeyMaster;
use Domain\TransactionNew;
use Domain\TransactionString;
use Domain\Factory\TransactionNewFactory;
use Domain\Factory\TransactionMiningFactory;




class TransactionTest extends TestCase
{

    private function createTransactionExists()
    {
        $km = new KeyMaster();
        $km->generateKey();

        $tr_arr = [
            'to' => '12345668890',
            'amount' => 100,
            'ttl' => 3600,
            'private_key' => $km->getPrivateKey(),
        ];

        $tn = new TransactionNew($tr_arr);

        $tn_string = $tn->toString();

        $te = (new TransactionString($tn_string))->fromString();

        return $te;
    }


    public function testTrCreateVerify()
    {
        $te = $this->createTransactionExists();

        $this->assertTrue($te->verifySign());
        $this->assertTrue($te->verifyTtl());
        $this->assertTrue($te->verifyHash());
    }

    public function test_verify_ttl()
    {
        $te = $this->createTransactionExists();

        $this->assertTrue($te->verifyTtl());
        $te->ttl = -10;
        $this->assertFalse($te->verifyTtl());
    }

    public function test_verify_sign()
    {
        $km = new KeyMaster();
        $km->generateKey();
        $fake_public_key = $km->getPublicKey();


        $te = $this->createTransactionExists();

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
        $te = $this->createTransactionExists();

        $this->assertEquals($te->ttl, $te->timeToSingBlock());
    }

    public function test_factory_transaction_new()
    {
        $tnf = new TransactionNewFactory();

        $tn = $tnf->produce();

        $te = (new TransactionString($tn->toString()))->fromString();

        $this->assertTrue($te->verifyHash());
        $this->assertTrue($te->verifySign());
        $this->assertTrue($te->verifyTtl());

    }

    public function test_factory_transaction_mining()
    {
        $tf = new TransactionMiningFactory();

        $t = $tf->produce();

        $te = (new TransactionString($t->toString()))->fromString();

        $this->assertTrue($te->verifyHash());
        $this->assertTrue($te->verifySign());
        $this->assertTrue($te->verifyTtl());
    }

}