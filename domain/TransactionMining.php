<?php

declare(strict_types = 1);

namespace Domain;

class TransactionMining extends TransactionExists
{
    private KeyMaster $km;


    public function __construct($data)
    {
        parent::__construct($data);

        $this->km = new KeyMaster($data['private_key']);
        $this->from = '0';
        $this->to = $this->km->getPublicKey(true);
        $this->sign = $this->calcSign();
        $this->hash = $this->calcHash();
    }


    public function calcSign() : string
    {
        return $this->km->getSign($this->makeString());
    }

}