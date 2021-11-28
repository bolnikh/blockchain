<?php

namespace Tests\Unit;


require_once __DIR__ . '\..\..\vendor\autoload.php';



use PHPUnit\Framework\TestCase;
use Domain\KeyMaster;
use Domain\TransactionMining;
use Domain\TransactionString;




class TransactionMiningTest extends TestCase
{

    public function test_create_transaction_mining()
    {
        $km = new KeyMaster();
        $km->generateKey();

        $tm = new TransactionMining([
            'private_key' => $km->getPrivateKey(),
            'amount' => 10,
            'ttl' => 3600,
        ]);

        $this->assertTrue($tm->isMining());
    }
}