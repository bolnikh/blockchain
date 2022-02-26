<?php


declare(strict_types=1);

namespace Domain\Factory;

use Domain\TrxMining;
use Domain\KeyMaster;

class TrxMiningFactory
{
    private string $private_key;
    private int $amount;

    private KeyMaster $km;

    public function __set($key, $val)
    {
        $this->$key = $val;
    }

    private function prepare() : void
    {
        if (empty($this->private_key)) {
            $this->km = new KeyMaster();
            $this->km->generateKey();

            $this->private_key = $this->km->getPrivateKey();
        }

        if (empty($this->amount)) {
            $this->amount = 100;
        }

    }

    public function produce() : TrxMining
    {
        $this->prepare();

        $tm = new TrxMining([
            'private_key' => $this->private_key,
            'amount' => $this->amount,
        ]);

        return $tm;
    }


    public function getPrivateKey() : string
    {
        return $this->private_key;
    }

}