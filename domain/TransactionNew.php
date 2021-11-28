<?php

declare(strict_types = 1);

namespace Domain;

class TransactionNew extends TransactionExists
{
    private KeyMaster $km;

    protected $fillable = [
        'to', 'amount', 'created_at', 'ttl'
    ];


    public function __construct($data)
    {
        parent::__construct($data);

        $this->km = new KeyMaster($data['private_key']);
        $this->from = $this->km->getPublicKey(true);
        $this->sign = $this->calcSign();
        $this->hash = $this->calcHash();
    }


    public function calcSign() : string
    {
        return $this->km->getSign($this->makeString());
    }

}