<?php

namespace Tests\Unit;

require_once __DIR__ . '\..\..\vendor\autoload.php';

use PHPUnit\Framework\TestCase;
use Domain\KeyMaster;
use Domain\Sign;



class KeyMasterTest extends TestCase
{
    private $key_file = 'data/key1.pem';

    public function test_generate_and_load_key()
    {
        chdir(__DIR__);
        $km = new KeyMaster();
        $km->generateKey();

        $this->assertTrue(strlen($km->getPublicKey()) > 100);
        $this->assertTrue(strlen($km->getPrivateKey()) > 400);

        $privateKey = $km->getPrivateKey();

        $km1 = new KeyMaster($privateKey);

        $this->assertEquals($km->getPublicKey(), $km1->getPublicKey());
        $this->assertEquals($km->getPrivateKey(), $km1->getPrivateKey());
    }

    public function test_sign()
    {
        $km = new KeyMaster();
        $km->generateKey();

        $data_to_sign = "01234567890 qwertyuiop";

        $sign = $km->getSign($data_to_sign);

        $this->assertTrue(strlen($sign) > 10);

        $this->assertTrue(Sign::check($data_to_sign, $sign, $km->getPublicKey()));
        $this->assertFalse(Sign::check($data_to_sign, $sign . $sign, $km->getPublicKey()));
    }
}