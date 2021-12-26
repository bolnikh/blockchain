<?php


declare(strict_types=1);

namespace Domain\Factory;

use Domain\TransactionNew;
use Domain\KeyMaster;

class TransactionNewFactory
{
    public string $private_key_from;
    public string $to;
    public int $amount;
    public int $created_at;
    public int $ttl;

    public $km_from;
    public $km_to;

    public function __set($key, $val)
    {
        $this->$key = $val;
    }

    private function prepare() : void
    {
        if (empty($this->private_key_from)) {
            $this->km_from = new KeyMaster();
            $this->km_from->generateKey();

            $this->private_key_from = $this->km_from->getPrivateKey();
        }

        if (empty($this->to)) {
            $this->km_to = new KeyMaster();
            $this->km_to->generateKey();

            $this->to = $this->km_to->getPublicKey(true);
        }

        if (empty($this->amount)) {
            $this->amount = 100;
        }

        if (empty($this->created_at)) {
            $this->created_at = time();
        }

        if (empty($this->ttl)) {
            $this->ttl = 3600;
        }
    }

    public function produce() : TransactionNew
    {
        $this->prepare();

        $tn = new TransactionNew([
            'private_key' => $this->private_key_from,
            'to' => $this->to,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'ttl' => $this->ttl,
        ]);

        return $tn;
    }


    public function getKmFrom() : ?KeyMaster
    {
        return $this->km_from;
    }

    public function getKmTo() : ?KeyMaster
    {
        return $this->km_to;
    }
}