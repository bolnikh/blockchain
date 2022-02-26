<?php

namespace Tests\Unit;


require_once __DIR__ . '\..\..\vendor\autoload.php';



use PHPUnit\Framework\TestCase;
use Domain\KeyMaster;
use Domain\TrxMining;
use Domain\TrxString;




class TrxMiningTest extends TestCase
{

    public function test_create_trx_mining()
    {
        $km = new KeyMaster();
        $km->generateKey();

        $tm = new TrxMining([
            'private_key' => $km->getPrivateKey(),
            'amount' => 10,
            'ttl' => 3600,
        ]);

        $this->assertTrue($tm->isMining());
    }
}