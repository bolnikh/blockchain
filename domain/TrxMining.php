<?php

declare(strict_types = 1);

namespace Domain;

class TrxMining extends TrxExists
{
    private KeyMaster $km;


    public function __construct($data)
    {
        $this->km = new KeyMaster($data['private_key']);
        $this->from = TrxExists::MINING_FROM;
        $this->to = $this->km->getPublicKey(true);
        $this->amount = $data['amount'];
        $this->created_at = time();
        $this->ttl = 30 * 24 * 3600;
        $this->sign = $this->calcSign();
        $this->hash = $this->calcHash();
    }


    public function calcSign() : string
    {
        return $this->km->getSign($this->makeString());
    }

}